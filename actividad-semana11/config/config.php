<?php
/**
 * Configuración global de la Actividad - Semana 11.
 * Si tu MySQL tiene contraseña o usa otro puerto, cámbialo aquí (un solo lugar).
 */

date_default_timezone_set('America/Lima');

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

const DB_HOST = 'localhost';
const DB_PORT = 3306;
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'actividad_semana11';   // nombre de la base de datos (la guía la llamaba "academico")

const APP_NAME = 'Actividad - Semana 11';
const APP_TEMA = 'Programación Web · PHP + MySQL con PDO';

const CDN_BOOTSTRAP_CSS = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css';
const CDN_BOOTSTRAP_JS  = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js';
