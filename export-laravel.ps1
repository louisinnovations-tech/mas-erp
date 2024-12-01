# Create temp directory if it doesn't exist
$tempDir = ".\temps"
New-Item -ItemType Directory -Force -Path $tempDir

# Define directories and files to copy
$directoriesToCopy = @(
    ".\app",
    ".\routes",
    ".\database\migrations",
    ".\database\seeders",
    ".\config",
    ".\resources\views",
    ".\tests"
)

# Define files to copy from root
$filesToCopy = @(
    "composer.json",
    "package.json",
    "webpack.mix.js",
    "artisan"
)

# Create function to sanitize .env file
function Get-SanitizedEnv {
    if (Test-Path .\.env) {
        $envContent = Get-Content .\.env
        $sanitizedEnv = $envContent | ForEach-Object {
            if ($_ -match '^[A-Za-z_]+=.+') {
                # Keep the key but replace value with placeholder
                $key = $_ -split '=', 2 | Select-Object -First 1
                "$key=PLACEHOLDER"
            } else {
                $_
            }
        }
        $sanitizedEnv | Set-Content "$tempDir\.env.example"
    }
}

# Copy directories
foreach ($dir in $directoriesToCopy) {
    if (Test-Path $dir) {
        $destDir = Join-Path $tempDir (Split-Path $dir -Leaf)
        Write-Host "Copying $dir to $destDir"
        Copy-Item -Path $dir -Destination $destDir -Recurse -Force
    }
}

# Copy individual files
foreach ($file in $filesToCopy) {
    if (Test-Path $file) {
        Write-Host "Copying $file to $tempDir"
        Copy-Item -Path $file -Destination $tempDir -Force
    }
}

# Create sanitized .env
Get-SanitizedEnv

# Remove any potential sensitive directories or files
$dirsToRemove = @(
    "$tempDir\storage\logs",
    "$tempDir\bootstrap\cache",
    "$tempDir\vendor",
    "$tempDir\node_modules"
)

foreach ($dir in $dirsToRemove) {
    if (Test-Path $dir) {
        Write-Host "Removing $dir"
        Remove-Item -Path $dir -Recurse -Force
    }
}

Write-Host "Done! Files exported to $tempDir"
Write-Host "Please review the contents before sharing to ensure no sensitive data is included."