<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\Admin\TableroController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\PermisoController;
use App\Http\Controllers\AutenticacionController;
use App\Http\Controllers\Admin\SucursalSelectionController;
use App\Http\Controllers\Admin\SucursalController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\ReportPdfController;

// Rutas de Invitado (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/', [AutenticacionController::class, 'mostrarFormulario'])->name('login');
    Route::post('/acceder', [AutenticacionController::class, 'acceder'])->name('acceder');
});

// Rutas Protegidas de Administración
Route::middleware(['auth', 'branch'])->prefix('admin')->name('admin.')->group(function () {

    // Selección de Sucursal
    Route::get('/seleccionar-sucursal', [SucursalSelectionController::class, 'seleccionar'])->name('sucursales.seleccionar');
    Route::post('/establecer-sucursal', [SucursalSelectionController::class, 'establecer'])->name('sucursales.establecer');

    // Tablero
    Route::get('/tablero', [TableroController::class, 'inicio'])->name('tablero');

    // Módulo de Usuarios
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'inicio'])->name('inicio');
        Route::get('/crear', [UsuarioController::class, 'crear'])->name('crear');
        Route::post('/guardar', [UsuarioController::class, 'guardar'])->name('guardar');
        Route::get('/{id}/editar', [UsuarioController::class, 'editar'])->name('editar');
        Route::put('/{id}/actualizar', [UsuarioController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{id}/eliminar', [UsuarioController::class, 'eliminar'])->name('eliminar');
    });

    // Módulo de Roles
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolController::class, 'inicio'])->name('inicio');
        Route::get('/crear', [RolController::class, 'crear'])->name('crear');
        Route::post('/guardar', [RolController::class, 'guardar'])->name('guardar');
        Route::get('/{id}/editar', [RolController::class, 'editar'])->name('editar');
        Route::put('/{id}/actualizar', [RolController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{id}/eliminar', [RolController::class, 'eliminar'])->name('eliminar');
    });

    // Módulo de Permisos
    Route::prefix('permisos')->name('permisos.')->group(function () {
        Route::get('/', [PermisoController::class, 'inicio'])->name('inicio');
        Route::post('/guardar', [PermisoController::class, 'guardar'])->name('guardar');
        Route::delete('/{id}/eliminar', [PermisoController::class, 'eliminar'])->name('eliminar');
    });

    // Módulo de Sucursales
    Route::prefix('sucursales')->name('sucursales.')->group(function () {
        Route::get('/', [SucursalController::class, 'inicio'])->name('inicio');
        Route::get('/crear', [SucursalController::class, 'crear'])->name('crear');
        Route::post('/guardar', [SucursalController::class, 'guardar'])->name('guardar');
        Route::get('/{id}/editar', [SucursalController::class, 'editar'])->name('editar');
        Route::put('/{id}/actualizar', [SucursalController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{id}/eliminar', [SucursalController::class, 'eliminar'])->name('eliminar');
    });

    // Módulo de Productos e Inventario
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [ProductoController::class, 'inicio'])->name('inicio');
        Route::get('/crear', [ProductoController::class, 'crear'])->name('crear');
        Route::post('/guardar', [ProductoController::class, 'guardar'])->name('guardar');
        Route::get('/{id}/editar', [ProductoController::class, 'editar'])->name('editar');
        Route::put('/{id}/actualizar', [ProductoController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{id}/eliminar', [ProductoController::class, 'eliminar'])->name('eliminar');
        Route::get('/inventario-global', [ProductoController::class, 'inventarioGlobal'])->name('inventario.global');
        Route::post('/reportar-defectuoso', [ProductoController::class, 'reportarDefectuoso'])->name('reportar.defectuoso');
        Route::get('/importar', \App\Livewire\Admin\InventoryImport::class)->name('importar');
    });

    // POS e Inventario con Livewire
    Route::get('/pos', \App\Livewire\Admin\PosComponent::class)->name('pos');

    // Módulo de Clientes
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ClienteController::class, 'inicio'])->name('inicio');
        Route::get('/crear', [\App\Http\Controllers\Admin\ClienteController::class, 'crear'])->name('crear');
        Route::post('/guardar', [\App\Http\Controllers\Admin\ClienteController::class, 'guardar'])->name('guardar');
        Route::get('/{id}/editar', [\App\Http\Controllers\Admin\ClienteController::class, 'editar'])->name('editar');
        Route::put('/{id}/actualizar', [\App\Http\Controllers\Admin\ClienteController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{id}/eliminar', [\App\Http\Controllers\Admin\ClienteController::class, 'eliminar'])->name('eliminar');
        Route::get('/search', [\App\Http\Controllers\Admin\ClienteController::class, 'search'])->name('search');
    });
    Route::get('/traslados', \App\Livewire\Admin\TransferIndex::class)->name('traslados.inicio');
    Route::get('/traslados/crear', \App\Livewire\Admin\TransferCreate::class)->name('traslados.crear');

    // Módulo de Cotizaciones
    Route::get('/cotizaciones', \App\Livewire\Admin\QuotationIndex::class)->name('quotations.index');
    Route::get('/cotizaciones/crear', \App\Livewire\Admin\QuotationCreate::class)->name('quotations.create');
    Route::get('/cotizaciones/{id}/editar', \App\Livewire\Admin\QuotationEdit::class)->name('quotations.edit');

    // Módulo de Ventas
    Route::get('/ventas', \App\Livewire\Admin\SaleIndex::class)->name('sales.index');

    // Módulo de Caja
    Route::get('/caja', \App\Livewire\Admin\CashRegisterIndex::class)->name('cash-register.index');
    Route::get('/cajas/gestionar', \App\Livewire\Admin\CajaManager::class)->name('cajas.index');

    // Reportes Avanzados
    Route::get('/reportes', \App\Livewire\Admin\ReportsDashboard::class)->name('reportes.dashboard');
    Route::get('/reportes/ventas', \App\Livewire\Admin\SalesReport::class)->name('reportes.ventas');
    Route::get('/reportes/traslados', \App\Livewire\Admin\TransfersReport::class)->name('reportes.traslados');
    Route::get('/reportes/productos', \App\Livewire\Admin\ProductSalesReport::class)->name('reportes.productos');
    Route::get('/reportes/defectuosos', [ProductoController::class, 'reporteDefectuosos'])->name('reportes.defectuosos');
    Route::post('/reportes/defectuosos/{id}/repuesto', [ProductoController::class, 'repuestoDefectuoso'])->name('reportes.defectuoso.repuesto');

    // Descargas de PDF
    Route::get('/reportes/pdf/ventas', [ReportPdfController::class, 'downloadSalesPdf'])->name('reportes.pdf.ventas');
    Route::get('/reportes/pdf/traslados', [ReportPdfController::class, 'downloadTransfersPdf'])->name('reportes.pdf.traslados');
    Route::get('/reportes/pdf/productos', [ReportPdfController::class, 'downloadProductsPdf'])->name('reportes.pdf.productos');
    Route::get('/reportes/pdf/reposicion', [ReportPdfController::class, 'downloadReplenishmentPdf'])->name('reportes.pdf.reposicion');
    Route::get('/reportes/pdf/defectuosos', [ReportPdfController::class, 'downloadDefectivePdf'])->name('reportes.pdf.defectuosos');
    Route::get('/cotizaciones/{id}/pdf', [ReportPdfController::class, 'downloadQuotationPdf'])->name('quotations.pdf');
    Route::get('/ventas/{id}/pdf', [ReportPdfController::class, 'downloadSaleDetailPdf'])->name('sales.pdf');
});

// Logout
Route::post('/salir', [AutenticacionController::class, 'salir'])->name('logout');
