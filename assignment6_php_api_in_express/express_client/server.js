const express = require("express");

const app = express();

app.get("/students", async (req, res) => {

    try {

        const response = await fetch(
            "http://localhost/PHP_Practical_Assignment/assignment6_php_api_in_express/php_api/students.php"
        );

        if (!response.ok) {
            return res.status(502).send("PHP API request failed.");
        }

        const students = await response.json();

        let html = `
            <h2>Student Details from PHP REST API</h2>
            <table border="1" cellpadding="10">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Course</th>
                </tr>
        `;

        for (const student of students) {

            html += `
                <tr>
                    <td>${escapeHtml(student.id)}</td>
                    <td>${escapeHtml(student.name)}</td>
                    <td>${escapeHtml(student.course)}</td>
                </tr>
            `;
        }

        html += "</table>";

        res.type("html").send(html);

    } catch (error) {

        console.error(error);

        res.status(500).send("Unable to connect to PHP API.");

    }
});

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (character) => {
        const entities = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"
        };

        return entities[character];
    });
}

const PORT = 3001;

app.listen(PORT, () => {
    console.log(`Express client running at http://localhost:${PORT}`);
});