# URL Routing Reference

## Clean URL Mappings

### Public Routes
- `/` → Redirects to `/home` (301)
- `/home` → `pages/home.php` (Homepage with featured project)
- `/about` → `pages/about.php`
- `/blog` → `pages/blog.php` (Article listing)
- `/projects` → `pages/projects.php` (Project listing)
- `/article/{slug}` → `pages/articleDetail.php` (Article detail)
- `/project/{id}` → `pages/projectDetail.php` (Project detail)

### Authentication Routes
- `/login` → `pages/adminSignIn.php`
- `/logout` → `pages/logout.php`
- `/setup` → `pages/setup.php` (First-time admin setup)

### Admin Routes (Require Authentication)
- `/addProject` → `pages/addProject.php`
- `/editProject/{id}` → `pages/editProject.php`
- `/deleteProject` → `pages/deleteProject.php` (POST only)
- `/addArticle` → `pages/addArticle.php`
- `/editArticle/{slug}` → `pages/editArticle.php`
- `/deleteArticle` → `pages/deleteArticle.php` (POST only)

### Error Handling
- Any unmatched route → `pages/404.php` (HTTP 404)

## Parameter Validation

### ID Parameters
- Must be positive integers
- Invalid format returns 404

### Slug Parameters
- Must match pattern: `/^[a-z0-9-]+$/` (lowercase letters, numbers, hyphens)
- Invalid format returns 404

## Trailing Slash Handling
- Trailing slashes are automatically stripped
- `/about` and `/about/` are treated identically
- Exception: Root `/` redirects to `/home`

## Static Assets
Static files (CSS, JS, images) bypass PHP routing:
- `/css/*` → Direct file access
- `/js/*` → Direct file access
- `/images/*` → Direct file access
- `/uploads/*` → Direct file access

Configured via Nginx `try_files $uri $uri/ /index.php?$query_string`

## Migration Notes

### Old URLs → New URLs
- `/index.php` → `/home`
- `/pages/about.php` → `/about`
- `/pages/blog.php` → `/blog`
- `/pages/projects.php` → `/projects`
- `/pages/projectDetail.php?id=5` → `/project/5`
- `/pages/articleDetail.php?slug=my-article` → `/article/my-article`
- `/pages/adminSignIn.php` → `/login`
- `/pages/addProject.php` → `/addProject`
- `/pages/editProject.php?id=5` → `/editProject/5`
- `/pages/editArticle.php?slug=my-article` → `/editArticle/my-article`

No backward compatibility layer implemented - hard cutover to clean URLs.
