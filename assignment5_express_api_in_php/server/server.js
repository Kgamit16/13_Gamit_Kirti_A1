const express = require("express");

const app = express();

app.use(express.json());

const students = [
    {
        id: 1,
        name: "Rahul",
        course: "BCA"
    },
    {
        id: 2,
        name: "Priya",
        course: "MSc ICT"
    },
    {
        id: 3,
        name: "Amit",
        course: "BSc IT"
    }
];

app.get("/api/students", (req, res) => {
    res.json(students);
});

app.get("/", (req, res) => {
    res.send("Express REST API is running.");
});

const PORT = 3000;

app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});