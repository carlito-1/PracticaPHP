//cogemos el forumlario por el id
let form = document.getElementById("form");
const inputs = form.elements;
for(let i = 0; i < inputs.length; i++){
    inputs[i].addEventListener("focus", function(){
        inputs[i].setAttribute("style", "background-color:#0202");
    inputs[i].addEventListener("keypress", function(){
        inputs[i].setAttribute("style", "border: solid 6px green");
    })
    })
    inputs[i].addEventListener("blur", function(){
        inputs[i].setAttribute("style", "");
    })
    inputs[i].addEventListener("mouseenter", function(){
        inputs[i].setAttribute("style", "background-color:#0202");
    })
    inputs[i].addEventListener("mouseleave", function(){
        inputs[i].setAttribute("style", "");
    })

}
