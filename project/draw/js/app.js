const canvas = document.getElementById("drawing-canvas");
const ctx = canvas.getContext("2d");

let shapes = JSON.parse(localStorage.getItem("shapes") || "[]");

class Circle {
    constructor(x, y, radius, color) {
        this.x = x;
        this.y = y;
        this.radius = radius;
        this.color = color;
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();
    }
}

class Square {
    constructor(x, y, size, color) {
        this.x = x;
        this.y = y;
        this.size = size;
        this.color = color;
    }

    draw(ctx) {
        ctx.fillStyle = this.color;
        ctx.fillRect(this.x, this.y, this.size, this.size);
    }
}

class Line {
    constructor(x1, y1, x2, y2, color) {
        this.x1 = x1;
        this.y1 = y1;
        this.x2 = x2;
        this.y2 = y2;
        this.color = color;
    }

    draw(ctx) {
        const size = document.getElementById("size-input").value;
        ctx.beginPath();
        ctx.moveTo(this.x1, this.y1);
        ctx.lineTo(this.x2, this.y2);
        ctx.lineWidth = size / 5;
        ctx.lineCap = "butt";
        ctx.strokeStyle = this.color;
        ctx.stroke();
    }
}

function saveShapesToLocalStorage() {
    localStorage.setItem("shapes", JSON.stringify(shapes));
}

function drawAllShapes() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    shapes.forEach(shape => {
        if (shape.type === "circle") {
            const circle = new Circle(shape.x, shape.y, shape.radius, shape.color);
            circle.draw(ctx);
        } else if (shape.type === "square") {
            const square = new Square(shape.x, shape.y, shape.size, shape.color);
            square.draw(ctx);
        } else if (shape.type === "line") {
            const line = new Line(shape.x1, shape.y1, shape.x2, shape.y2, shape.color);
            line.draw(ctx);
        }
    });
}

document.getElementById("draw-button").addEventListener("click", function () {
    const shapeType = document.getElementById("shape-select").value;
    const color = document.getElementById("color-input").value;
    const size = document.getElementById("size-input").value;

    let shape;
    const pos1 = document.getElementById("x-input").value;
    const pos2 = document.getElementById("y-input").value;
    const pos3 = document.getElementById("x2-input").value;
    const pos4 = document.getElementById("y2-input").value;

    if (shapeType === "circle") {
        shape = { type: "circle", x: parseFloat(pos1), y: parseFloat(pos2), radius: parseFloat(size), color: color };
    } else if (shapeType === "square") {
        shape = { type: "square", x: parseFloat(pos1), y: parseFloat(pos2), size: parseFloat(size), color: color };
    } else if (shapeType === "line") {
        shape = { type: "line", x1: parseFloat(pos1), y1: parseFloat(pos2), x2: parseFloat(pos3), y2: parseFloat(pos4), color: color };
    } else if (shapeType === "free") {
        // Freehand drawing is handled separately
        return;
    }

    shapes.push(shape);
    saveShapesToLocalStorage();  // Save after drawing a new shape
    drawAllShapes();
});

// Event listener for undo button
document.getElementById("undo-button").addEventListener("click", function () {
    const shapeType = document.getElementById("shape-select").value;

    // Only undo if the current mode is not freehand drawing
    if (shapeType !== "free") {
        shapes.pop(); // Remove the last shape from the shapes array
        saveShapesToLocalStorage(); // Update local storage after undo
        drawAllShapes(); // Redraw all shapes
    }
});

// Rest of your code remains unchanged...

document.getElementById("clear-button").addEventListener("click", function () {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    shapes = [];
    localStorage.removeItem("shapes");
    drawAllShapes();
});

window.addEventListener('load', function () {
    const savedShapes = JSON.parse(localStorage.getItem("shapes") || "[]");
    if (savedShapes.length > 0) {
        shapes = savedShapes;
        drawAllShapes();
    }
});

function hide() {
    const shapeType = document.getElementById("shape-select").value;
    let h = document.getElementById("clearButton");
    let u = document.getElementById("undo-button");
    let c = document.getElementById("clear-button");
    let e = document.getElementById("X-axis");
    let f = document.getElementById("Y-axis");
    let j = document.getElementById("x-input");
    let k = document.getElementById("y-input");
    let l = document.getElementById("x2-input");
    let m = document.getElementById("y2-input");
    let n = document.getElementById("X2-axis");
    let o = document.getElementById("Y2-axis");

    if (shapeType === "line") {
        h.style.display = "none";
        u.style.display = "block"; // Show undo button
        c.style.display = "block";
        e.style.display = "block";
        f.style.display = "block";
        j.style.display = "block";
        k.style.display = "block";
        l.style.display = "block";
        m.style.display = "block";
        n.style.display = "block";
        o.style.display = "block";
    } else if (shapeType === "circle" || shapeType === "square") {
        h.style.display = "none";
        u.style.display = "block"; // Show undo button
        c.style.display = "block";
        e.style.display = "block";
        f.style.display = "block";
        j.style.display = "block";
        k.style.display = "block";
        l.style.display = "none";
        m.style.display = "none";
        n.style.display = "none";
        o.style.display = "none";
    } else if (shapeType === "free") {
        h.style.display = "none";
        u.style.display = "none"; // Hide undo button
        c.style.display = "block";
        e.style.display = "none";
        f.style.display = "none";
        j.style.display = "none";
        k.style.display = "none";
        l.style.display = "none";
        m.style.display = "none";
        n.style.display = "none";
        o.style.display = "none";
    }
}

function free() {
    let drawing = false;
    let lastX = 0;
    let lastY = 0;

    canvas.addEventListener("mousedown", function (e) {
        drawing = true;
        lastX = e.offsetX;
        lastY = e.offsetY;
    });

    canvas.addEventListener("mousemove", function (e) {
        if (!drawing) return;

        const currentX = e.offsetX;
        const currentY = e.offsetY;

        // Get the current color and size
        const color = document.getElementById("color-input").value;
        const size = document.getElementById("size-input").value;

        // Set the line properties
        ctx.lineWidth = size / 5;
        ctx.lineCap = "round";
        ctx.strokeStyle = color;

        // Draw the line
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(currentX, currentY);
        ctx.stroke();

        lastX = currentX;
        lastY = currentY;
    });

    canvas.addEventListener("mouseup", function () {
        drawing = false;
    });

    canvas.addEventListener("mouseout", function () {
        drawing = false;
    });
}

// Event listener for shape select change
document.getElementById("shape-select").addEventListener("change", function () {
    const shapeType = this.value;
    hide(); // Update UI visibility

    if (shapeType === "free") {
        free(); // Handle freehand drawing
    }
});