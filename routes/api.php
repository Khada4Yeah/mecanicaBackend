<?php

use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\FichaReparacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AdministradorController;

use Illuminate\Support\Facades\Route;

//?? RUTAS DE USUARIOS ?/
//** API de usuarios */
Route::apiResource("usuarios", UsuarioController::class)->middleware(
    "auth:api",
);

//?? RUTAS DE ADMINISTRADORES ?/
//** Ruta para logearse en el sistema */
Route::post("administradores/login", [
    AdministradorController::class,
    "login",
])->name("login");
Route::post("administradores/validarToken", [
    AdministradorController::class,
    "obtenerUsuarioAutenticado",
]);
Route::post("administradores/logout", [
    AdministradorController::class,
    "logout",
]);

//** API de administradores */
Route::apiResource(
    "administradores",
    AdministradorController::class,
)->middleware("auth:api");

//?? RUTAS DE CLIENTES ?/
//** API de clientes */
Route::apiResource("clientes", ClienteController::class)->middleware(
    "auth:api",
);

//?? RUTAS DE VEHICULOS ?/
//** API de vehiculos */
Route::apiResource("vehiculos", VehiculoController::class)->middleware(
    "auth:api",
);

//** Ruta para obtener los vehiculos de un cliente */
Route::get("vehiculos/cliente/{parametro}", [
    VehiculoController::class,
    "vehiculosCliente",
])->middleware("auth:api");

//?? RUTAS DE REPARACIONES ?/
//** API de reparaciones */
Route::apiResource("reparaciones", ReparacionController::class)->middleware(
    "auth:api",
);

//?? RUTAS DE FICHAS ?/
//** API de fichas */
Route::apiResource("fichas", FichaController::class)->middleware("auth:api");

//** Ruta para obtener las fichas de un cliente para un vehículo */
Route::get("fichas/cliente/vehiculo/{id_vehiculo}", [
    FichaController::class,
    "fichasClienteVehiculo",
])->middleware("auth:api");

//** Ruta para generar el PDF de la ficha */
Route::get("fichas/pdf/{id}", [
    FichaController::class,
    "generarPdfFicha",
])->middleware("auth:api");

//?? RUTAS DE FICHASREPARACIONES ?/
//** API de fichasreparaciones */
Route::apiResource(
    "fichasreparaciones",
    FichaReparacionController::class,
)->middleware("auth:api");
