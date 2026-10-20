
const express = require('express');
const axios = require('axios');

const app = express();

// Call PHP REST API
app.get('/students', async (req, res) => {
    try {
        const response = await axios.get(
            'http://localhost/studentxml/students.php'
        );

        res.json(response.data);
    } catch (error) {
        res.status(500).json({
            message: 'Error calling PHP REST API'
        });
    }
});

app.listen(3000, () => {
    console.log('Express server running on port 3000');
});
