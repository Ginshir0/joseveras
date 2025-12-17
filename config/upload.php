<?php
// config/upload.php
// Secure image upload handling

/**
 * Handle image upload with comprehensive security validation
 * 
 * @param array $file The $_FILES['fieldname'] array
 * @param string $upload_dir Directory to save uploaded files
 * @param string|null $old_filename Optional: old filename to delete on success
 * @param int $max_size_bytes Maximum file size in bytes (default 2MB)
 * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function handle_image_upload(
    array $file, 
    string $upload_dir, 
    ?string $old_filename = null,
    int $max_size_bytes = 2097152 // 2MB
): array {
    // Check if file was uploaded
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => null, 'error' => null];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the server size limit.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the form size limit.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error: missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Server error: failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'File upload was stopped by a server extension.'
        ];
        $error = $error_messages[$file['error']] ?? 'Unknown upload error.';
        return ['success' => false, 'filename' => null, 'error' => $error];
    }
    
    // Validate file size (server-side)
    if ($file['size'] > $max_size_bytes) {
        $max_mb = $max_size_bytes / 1024 / 1024;
        return [
            'success' => false, 
            'filename' => null, 
            'error' => "File size exceeds maximum allowed ({$max_mb}MB)."
        ];
    }
    
    // Validate file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed_extensions)) {
        return [
            'success' => false, 
            'filename' => null, 
            'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_extensions)
        ];
    }
    
    // Validate MIME type using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'image/jpeg',
        'image/png', 
        'image/gif',
        'image/webp'
    ];
    if (!in_array($mime_type, $allowed_mimes)) {
        return [
            'success' => false, 
            'filename' => null, 
            'error' => 'Invalid image type. File content does not match an allowed image format.'
        ];
    }
    
    // Validate that file is actually an image using getimagesize
    $image_info = @getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return [
            'success' => false, 
            'filename' => null, 
            'error' => 'The file does not appear to be a valid image.'
        ];
    }
    
    // Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return [
                'success' => false, 
                'filename' => null, 
                'error' => 'Server error: could not create upload directory.'
            ];
        }
    }
    
    // Generate unique filename
    $new_filename = uniqid('proj_', true) . '.' . $ext;
    $destination = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $new_filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return [
            'success' => false, 
            'filename' => null, 
            'error' => 'Failed to save uploaded file.'
        ];
    }
    
    // Delete old file if specified and exists
    if ($old_filename) {
        $old_path = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $old_filename;
        if (file_exists($old_path)) {
            @unlink($old_path);
        }
    }
    
    return ['success' => true, 'filename' => $new_filename, 'error' => null];
}
