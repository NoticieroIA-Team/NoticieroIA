require('dotenv').config();
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');
const { conectar } = require('./db');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());           // Permite solicitudes desde cualquier origen
app.use(bodyParser.json()); // Permite leer JSON del body

// Servir archivos estáticos (desde /app ya que server.js está en /app)
app.use('/css', express.static(path.join(__dirname, './css')));
app.use('/js', express.static(path.join(__dirname, './js')));
app.use('/img', express.static(path.join(__dirname, './img')));
app.use('/vistas', express.static(path.join(__dirname, './vistas')));

// Rutas para las vistas HTML
app.get('/', (req, res) => {
  const filePath = path.join(__dirname, './vistas/login.html');
  console.log('Serving login.html from:', filePath);
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving login.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

app.get('/login', (req, res) => {
  const filePath = path.join(__dirname, './vistas/login.html');
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving login.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

app.get('/home', (req, res) => {
  const filePath = path.join(__dirname, './vistas/home.html');
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving home.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

app.get('/articulos', (req, res) => {
  const filePath = path.join(__dirname, './vistas/articulos.html');
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving articulos.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

// API Info endpoint
app.get('/api', (req, res) => {
  res.json({
    status: 'ok',
    message: 'NoticieroIA API',
    version: '1.0.0',
    endpoints: {
      health: '/health',
      generos: 'POST /api/generos'
    }
  });
});

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

app.post('/api/generos', async (req, res) => {
  try {
    console.log('Datos recibidos del formulario:', req.body); // <-- Aquí se ve qué datos llegan
    const db = await conectar();
    const coleccion = db.collection('planificacioncontenido');

    const genero = req.body;

    const resultado = await coleccion.insertOne({
      ...genero,
      fecha_ingreso: new Date()
    });

    console.log('Documento insertado con ID:', resultado.insertedId); // <-- DEBUG
    res.json({ status: 'ok', id: resultado.insertedId });
  } catch (err) {
    console.error(err);
    res.status(500).json({ status: 'error', error: err.message });
  }
});

// Middleware para manejar 404
app.use((req, res) => {
  console.log(`❌ Ruta no encontrada: ${req.method} ${req.url}`);
  res.status(404).json({ error: 'Ruta no encontrada', path: req.url });
});

// Start server on 0.0.0.0 to be accessible from outside the container
app.listen(PORT, '0.0.0.0', () => {
  console.log(`✅ Servidor corriendo en http://0.0.0.0:${PORT}`);
  console.log(`📂 Directorio de trabajo: ${__dirname}`);
  console.log(`📂 Rutas de archivos estáticos:`);
  console.log(`   CSS: ${path.join(__dirname, './css')}`);
  console.log(`   JS: ${path.join(__dirname, './js')}`);
  console.log(`   Imágenes: ${path.join(__dirname, './img')}`);
  console.log(`   Vistas: ${path.join(__dirname, './vistas')}`);
  console.log(`📍 Páginas disponibles:`);
  console.log(`   GET  / - Login`);
  console.log(`   GET  /home - Home`);
  console.log(`   GET  /articulos - Artículos`);
  console.log(`📍 API Endpoints:`);
  console.log(`   GET  /api - API info`);
  console.log(`   GET  /health - Health check`);
  console.log(`   POST /api/generos - Insertar contenido`);
  console.log(`🔧 MongoDB URI configurado: ${process.env.MONGODB_URI ? 'Sí' : 'No'}`);
});
