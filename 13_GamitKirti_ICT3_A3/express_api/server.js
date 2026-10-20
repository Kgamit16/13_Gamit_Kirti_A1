
const express = require('express');
const app = express();

app.use(express.json());

// REST API
app.get('/students', (req, res) => {
    const students = [
        { id: 1, name: 'Rahul', course: 'BCA' },
        { id: 2, name: 'Priya', course: 'MCA' },
        { id: 3, name: 'Amit', course: 'BSc IT' }
    ];

    res.json(students);
});

app.listen(3000, () => {
    console.log('Server running at http://localhost:3000');
});
