#!/bin/sh
# Script para preparar los archivos necesarios para el build de Docker
# cuando el contexto de build es AIContentCreator
#
# Este script copia los archivos de beta/node y beta/vistas, etc. a AIContentCreator
# para que Docker pueda acceder a ellos desde el contexto de build

echo "📦 Preparando archivos para build de Docker..."

# Crear directorios necesarios en AIContentCreator
mkdir -p build-files/node build-files/vistas build-files/css build-files/js build-files/img

# Copiar archivos de Node.js
if [ -d "../beta/node" ]; then
  echo "✅ Copiando archivos de Node.js..."
  cp -r ../beta/node/* build-files/node/ 2>/dev/null || echo "⚠️  Error copiando archivos de Node.js"
else
  echo "❌ Error: Directorio ../beta/node no encontrado"
  exit 1
fi

# Copiar archivos estáticos si existen
if [ -d "../beta/vistas" ]; then
  echo "✅ Copiando vistas..."
  cp -r ../beta/vistas/* build-files/vistas/ 2>/dev/null || echo "⚠️  Vistas no encontradas"
fi

if [ -d "../beta/css" ]; then
  echo "✅ Copiando CSS..."
  cp -r ../beta/css/* build-files/css/ 2>/dev/null || echo "⚠️  CSS no encontrado"
fi

if [ -d "../beta/js" ]; then
  echo "✅ Copiando JS..."
  cp -r ../beta/js/* build-files/js/ 2>/dev/null || echo "⚠️  JS no encontrado"
fi

if [ -d "../beta/img" ]; then
  echo "✅ Copiando imágenes..."
  cp -r ../beta/img/* build-files/img/ 2>/dev/null || echo "⚠️  Imágenes no encontradas"
fi

echo "✅ Preparación completada"

