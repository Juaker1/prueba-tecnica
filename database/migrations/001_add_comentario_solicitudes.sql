-- ==========================================================
-- Migración 001: Agregar columna comentario a solicitudes
--
-- Ejecutar si ya importaste schema.sql previamente
-- y necesitas agregar esta columna a la BD existente.
-- ==========================================================

USE prueba_tecnica;

ALTER TABLE solicitudes
    ADD COLUMN comentario TEXT NULL
    AFTER estado;
