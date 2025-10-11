# Script de prueba para API - Clinica del Dolor Piura
# Endpoints disponibles

$API_URL = "https://apichatbot.clinicadeldolorpiura.com/apiweb.php"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Test API - Clinica del Dolor Piura" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Get Empresa
Write-Host "Test 1: Obtener Empresa..." -ForegroundColor Yellow
$body1 = @{
    op = "get_Empresa"
} | ConvertTo-Json

try {
    $response1 = Invoke-RestMethod -Uri $API_URL -Method Post -Body $body1 -ContentType "application/json"
    Write-Host "Exito:" -ForegroundColor Green
    $response1 | ConvertTo-Json -Depth 5
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "----------------------------------------"
Write-Host ""

# Test 2: Get Categorias
Write-Host "Test 2: Obtener Categorias..." -ForegroundColor Yellow
$body2 = @{
    op = "getCategorias"
} | ConvertTo-Json

try {
    $response2 = Invoke-RestMethod -Uri $API_URL -Method Post -Body $body2 -ContentType "application/json"
    Write-Host "✅ Éxito:" -ForegroundColor Green
    $response2 | ConvertTo-Json -Depth 5
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n----------------------------------------`n"

# Test 3: Get Servicios
Write-Host "🏥 Test 3: Obtener Servicios..." -ForegroundColor Yellow
$body3 = @{
    op = "getServicios"
} | ConvertTo-Json

try {
    $response3 = Invoke-RestMethod -Uri $API_URL -Method Post -Body $body3 -ContentType "application/json"
    Write-Host "✅ Éxito:" -ForegroundColor Green
    $response3 | ConvertTo-Json -Depth 5
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n----------------------------------------`n"

# Test 4: Get Especialidades
Write-Host "⚕️ Test 4: Obtener Especialidades..." -ForegroundColor Yellow
$body4 = @{
    op = "listar_especialidades"
} | ConvertTo-Json

try {
    $response4 = Invoke-RestMethod -Uri $API_URL -Method Post -Body $body4 -ContentType "application/json"
    Write-Host "✅ Éxito:" -ForegroundColor Green
    $response4 | ConvertTo-Json -Depth 5
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n----------------------------------------`n"

# Test 5: Get Preguntas Frecuentes
Write-Host "❓ Test 5: Obtener Preguntas Frecuentes..." -ForegroundColor Yellow
$body5 = @{
    op = "listar_preguntas"
} | ConvertTo-Json

try {
    $response5 = Invoke-RestMethod -Uri $API_URL -Method Post -Body $body5 -ContentType "application/json"
    Write-Host "✅ Éxito:" -ForegroundColor Green
    $response5 | ConvertTo-Json -Depth 5
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Tests completados!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
