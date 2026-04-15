import PDFDocument from 'pdfkit';
import express, { Request, Response } from 'express';
import mysql from 'mysql2';
import cors from 'cors';

const app = express();

// Middlewares
app.use(cors());
app.use(express.json());

// Configuración de la conexión a MySQL
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root', 
    password: '', 
    database: 'sistema_comunitario'
});

db.connect((err) => {
    if (err) {
        console.error('hubo un error conectando a la BD:', err);
        return;
    }
    console.log('¡Conectado a la base de datos MySQL al pelo (TypeScript Edition)!');
});

// --- INTERFACES (El chaleco antibalas de TS) ---
// Aquí le decimos a TS exactamente qué datos esperar del frontend
interface HabitanteBody {
    nombre: string;
    cedula: string;
    tipo_poblacion: string;
    sisben: number;
    es_desplazado: boolean;
    zona_id: string;
}

// --- RUTAS ---

app.get('/', (req: Request, res: Response) => {
    res.send('Servidor TS de la comunidad activo y ready');
});

// Ruta para obtener subsidios
app.get('/api/subsidios', (req: Request, res: Response) => {
    const query = 'SELECT * FROM subsidios';
    db.query(query, (err, results) => {
        if (err) {
            console.error('Error sacando los subsidios:', err);
            return res.status(500).json({ mensaje: 'Error interno del servidor' });
        }
        res.json(results);
    });
});

// Ruta para guardar un habitante (Con tipado estricto)
app.post('/api/habitantes', (req: Request<{}, {}, HabitanteBody>, res: Response) => {
    const { nombre, cedula, tipo_poblacion, sisben, es_desplazado, zona_id } = req.body;
    
    // Lógica para calcular vulnerabilidad automáticamente
    let vulnerabilidad = 'Baja';
    if (sisben < 40 || es_desplazado) {
        vulnerabilidad = 'Alta';
    } else if (sisben >= 40 && sisben < 70) {
        vulnerabilidad = 'Media';
    }

    const query = `INSERT INTO habitantes (nombre, cedula, tipo_poblacion, sisben, es_desplazado, nivel_vulnerabilidad, zona_id) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)`;

    db.query(query, [nombre, cedula, tipo_poblacion, sisben, es_desplazado, vulnerabilidad, zona_id], (err, result: any) => {
        if (err) {
            console.error(err);
            return res.status(500).json({ mensaje: 'Error al registrar habitante' });
        }
        res.status(201).json({ 
            mensaje: 'Habitante registrado fino', 
            id: result.insertId, 
            nivel_asignado: vulnerabilidad 
        });
    });
});
// Ruta mágica para generar el PDF de la Alcaldía
app.get('/api/reporte-alcaldia', (req: Request, res: Response) => {
    // Sacamos un resumen rápido de la base de datos
    const query = 'SELECT nivel_vulnerabilidad, COUNT(*) as total FROM habitantes GROUP BY nivel_vulnerabilidad';
    
    db.query(query, (err, results: any[]) => {
        if (err) {
            console.error('Peo generando el reporte:', err);
            return res.status(500).json({ mensaje: 'Error interno' });
        }

        // Creamos el documento PDF
        const doc = new PDFDocument();

        // Le decimos al navegador que esto es un PDF descargable
        res.setHeader('Content-disposition', 'attachment; filename=Informe_Comunidad_Mensual.pdf');
        res.setHeader('Content-type', 'application/pdf');

        // Conectamos el PDF con la respuesta del servidor
        doc.pipe(res);

        // --- DISEÑO DEL PDF ---
        doc.fontSize(22).text('Informe Mensual de Gestión Comunitaria', { align: 'center' });
        doc.moveDown();
        doc.fontSize(12).text(`Fecha de generación: ${new Date().toLocaleDateString()}`, { align: 'right' });
        doc.moveDown(2);

        doc.fontSize(16).text('Resumen del Censo Poblacional por Vulnerabilidad:');
        doc.moveDown();

        // Recorremos los datos y los metemos al PDF
        let totalHabitantes = 0;
        results.forEach(fila => {
            doc.fontSize(14).text(`- Nivel ${fila.nivel_vulnerabilidad}: ${fila.total} habitantes registrados`);
            totalHabitantes += fila.total;
        });

        doc.moveDown();
        doc.fontSize(14).text(`Total de habitantes en el sistema: ${totalHabitantes}`, { underline: true });

        // Sellamos y cerramos el PDF
        doc.end();
    });
});
// Arrancar el servidor
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Servidor TS corriendo a toda máquina en el puerto ${PORT}`);
});
// Ruta para las estadísticas del Dashboard
app.get('/api/estadisticas', (req: Request, res: Response) => {
    // Le pedimos a MySQL que cuente cuántos hay de cada nivel
    const query = 'SELECT nivel_vulnerabilidad, COUNT(*) as total FROM habitantes GROUP BY nivel_vulnerabilidad';
    
    db.query(query, (err, results) => {
        if (err) {
            console.error('Peo sacando las estadísticas:', err);
            return res.status(500).json({ mensaje: 'Error interno del servidor' });
        }
        res.json(results);
    });
});