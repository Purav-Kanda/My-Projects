document.addEventListener("DOMContentLoaded", function () {
    let currentRabbit = 0;
    let rabbits = document.querySelectorAll("img");
    let Eggs = document.getElementById("noeggs");
    let Slow = document.getElementById("slow");
    let attempts = 0;
    Eggs.style.visibility = "hidden";
    Slow.style.visibility = "hidden";
    for (let i = 1; i < rabbits.length; i++) {
        rabbits[i].style.visibility = "hidden";
    }

    function moveRabbit() {
        attempts=attempts+1
        rabbits[currentRabbit].style.visibility = "hidden";
        currentRabbit = (currentRabbit + 1) % rabbits.length;
        rabbits[currentRabbit].style.visibility = "visible";
        if (attempts>=4){
            Eggs.style.visibility = "visible";

        }
        if (attempts >=20){
            Slow.style.visibility = "visible";
        }
    }

    rabbits.forEach(rabbit => {
        rabbit.addEventListener("mousemove", moveRabbit);
    });
});
