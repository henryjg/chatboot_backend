# Test API - Clinica del Dolor Piura

$API_URL = "https://apichatbot.clinicadeldolorpiura.com/apiweb.php"

Write-Host "========================================"
Write-Host "Test API - Clinica del Dolor Piura"
Write-Host "========================================"
Write-Host ""

# Test 1
Write-Host "Test 1: get_Empresa" -ForegroundColor Yellow
$body = '{"op":"get_Empresa"}' 
Invoke-RestMethod -Uri $API_URL -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json

Write-Host ""
Write-Host "----------------------------------------"
Write-Host ""

# Test 2
Write-Host "Test 2: getCategorias" -ForegroundColor Yellow
$body = '{"op":"getCategorias"}' 
Invoke-RestMethod -Uri $API_URL -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json

Write-Host ""
Write-Host "----------------------------------------"
Write-Host ""

# Test 3
Write-Host "Test 3: getServicios" -ForegroundColor Yellow
$body = '{"op":"getServicios"}' 
Invoke-RestMethod -Uri $API_URL -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json

Write-Host ""
Write-Host "----------------------------------------"
Write-Host ""

# Test 4
Write-Host "Test 4: listar_especialidades" -ForegroundColor Yellow
$body = '{"op":"listar_especialidades"}' 
Invoke-RestMethod -Uri $API_URL -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json

Write-Host ""
Write-Host "========================================"
Write-Host "Tests completados!"
Write-Host "========================================"
