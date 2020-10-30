
//console.log("mark_star");

window.onload = function() {

	const stars = document.getElementsByName('star');
	const mark = document.getElementsByName('mark')[0];
	const cancel = document.getElementsByName('cancel')[0];

	//console.log(stars);
	//console.log(mark);

	stars.forEach(function(star){
		star.onclick = function() {
			let num = this.getAttribute('num');
			for(let i = 1; i <= 5; i++) {
				if(i <= num)
					stars[i-1].setAttribute("class", "star_checked");
				else
					stars[i-1].setAttribute("class", "star_not_checked");
			}
			mark.setAttribute("value", num);
			//console.log(mark);
		}
	});

	cancel.onclick = function() {
		for(let i = 0; i < 5; i++)
			stars[i].setAttribute("class", "star_not_checked");
		mark.setAttribute("value", "");
		//console.log(mark);
	}

}
