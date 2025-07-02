document.addEventListener("DOMContentLoaded", function () {
    let num1 = document.getElementById("num1"); 
    let num2 = document.getElementById("num2");
    let oper = document.getElementById("operation");
    let ans = document.getElementById("ans");

    function opp() {
        let number1 = parseFloat(num1.value) || 0; 
        let number2 = parseFloat(num2.value) || 0;
        let operation = oper.value;

        let sol;
        if (operation === "+") {
            sol = number1 + number2;
        } else if (operation === "-") {
            sol = number1 - number2;
        } else if (operation === "*") {
            sol = number1 * number2;
        } else if (operation === "/") {
            sol = number2 !== 0 ? number1 / number2 : "Error"; 
        } else if (operation === "%") {
            sol = number2 !== 0 ? number1 % number2 : "Error";
        } else {
            sol = "Invalid Operation";
        }

        ans.value = sol; 
    }

    num1.addEventListener("input", opp);
    num2.addEventListener("input", opp);
    oper.addEventListener("change", opp);

    opp();
});
