#!/usr/bin/env bash
set -euo pipefail

BASE="http://localhost:8080"
COOKIEJAR="/tmp/test_setup_cookies.txt"
HEADERS="/tmp/test_setup_headers.txt"
BODY="/tmp/test_setup_body.html"

rm -f "$COOKIEJAR" "$HEADERS" "$BODY"

# Ensure DB has no admins so setup is required (reset state)
# NOTE: uses docker-compose service 'db' and reads root password from .env
export MYSQL_ROOT_PASSWORD="*&VzuECWOVBk854P"
docker compose exec -T -e MYSQL_PWD="$MYSQL_ROOT_PASSWORD" db mysql -u root -e "DELETE FROM jvdb.admins;" || true

# GET the setup page and store cookie + body
curl -s -c "$COOKIEJAR" "$BASE/pages/setup.php" -o "$BODY"
TOKEN=$(grep -oP 'name="csrf_token" value="\K[^"]+' "$BODY" || true)
if [ -z "$TOKEN" ]; then
  echo "ERROR: CSRF token not found in setup page"
  exit 2
fi

echo "Found token: ${TOKEN:0:8}..."

# Negative test: POST without token (should fail) using a FRESH session (no cookie)
COOKIEJAR2="/tmp/test_setup_cookies2.txt"
rm -f "$COOKIEJAR2"
HTTP_STATUS2=$(curl -s -b "$COOKIEJAR2" -c "$COOKIEJAR2" -D "$HEADERS" -o "$BODY" --max-time 10 -w "%{http_code}" \
  --data-urlencode "username=testadmin" \
  --data-urlencode "password=IntegrationTest1@" \
  --data-urlencode "confirm_password=IntegrationTest1@" \
  "$BASE/pages/setup.php")

if grep -q "Invalid request" "$BODY" || [ "$HTTP_STATUS2" = "200" ]; then
  echo "SUCCESS: Missing token POST failed as expected"
else
  echo "FAIL: Missing token POST did not fail as expected (status $HTTP_STATUS2)"
  echo "Response body:" && sed -n '1,120p' "$BODY" || true
  exit 1
fi

# Post with token (valid request) to create admin
HTTP_STATUS=$(curl -s -b "$COOKIEJAR" -c "$COOKIEJAR" -D "$HEADERS" -o /dev/null --max-time 10 -w "%{http_code}" \
  --data-urlencode "csrf_token=$TOKEN" \
  --data-urlencode "username=testadmin" \
  --data-urlencode "password=IntegrationTest1@" \
  --data-urlencode "confirm_password=IntegrationTest1@" \
  "$BASE/pages/setup.php")

if [ "$HTTP_STATUS" = "302" ]; then
  LOCATION=$(grep -i '^Location:' "$HEADERS" | awk '{print $2}' | tr -d '\r')
  if [[ "$LOCATION" == */pages/projects.php ]]; then
    echo "SUCCESS: Setup form POST redirected to projects page"
  else
    echo "WARN: Setup POST redirected to $LOCATION (expected /pages/projects.php)"
  fi
else
  echo "FAIL: Expected 302 redirect but got HTTP $HTTP_STATUS"
  # Fetch body to show error messages
  curl -s -b "$COOKIEJAR" "$BASE/pages/setup.php" -o "$BODY"
  grep -i "Invalid request" -n "$BODY" || true
  exit 1
fi

echo "Integration test completed successfully"