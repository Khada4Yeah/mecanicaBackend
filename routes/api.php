<?php

use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\FichaReparacionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AdministradorController;
use App\Models\Administrador;
use Illuminate\Support\Facades\Route;

//?? RUTAS DE USUARIOS ?/
//** API de usuarios */
Route::apiResource("usuarios", UsuarioController::class);

//?? RUTAS DE ADMINISTRADORES ?/
//** Ruta para logearse en el sistema */
Route::post("administradores/login", [AdministradorController::class, "login"]);
Route::get("administradores/validarToken", [
    AdministradorController::class,
    "getAuthenticatedUser",
]);

//** API de administradores */
Route::apiResource("administradores", AdministradorController::class);

//?? RUTAS DE CLIENTES ?/
//** API de clientes */
Route::apiResource("clientes", ClienteController::class);

//?? RUTAS DE VEHICULOS ?/
//** API de vehiculos */
Route::apiResource("vehiculos", VehiculoController::class);

//** Ruta para obtener los vehiculos de un cliente */
Route::get("vehiculos/cliente/{parametro}", [
    VehiculoController::class,
    "vehiculosCliente",
]);

//?? RUTAS DE REPARACIONES ?/
//** API de reparaciones */
Route::apiResource("reparaciones", ReparacionController::class);

//?? RUTAS DE FICHAS ?/
//** API de fichas */
Route::apiResource("fichas", FichaController::class);

//** Ruta para obtener las fichas de un cliente */
Route::get("fichas/cliente/{parametro}", [
    FichaController::class,
    "fichasCliente",
]);

//** Ruta para generar el PDF de la ficha */
Route::get("fichas/pdf/{id}", [FichaController::class, "generarPdfFicha"]);

//?? RUTAS DE FICHASREPARACIONES ?/
//** API de fichasreparaciones */
Route::apiResource("fichasreparaciones", FichaReparacionController::class);