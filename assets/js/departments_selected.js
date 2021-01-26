
window.onload = function() {

	const select_all = document.getElementsByName("choice_department[select_all]")[0];
	const departments = document.getElementsByName("choice_department[area][]");
	
	if(select_all.checked) {
		for(let i = 0; i < departments.length; i++)
			departments[i].checked = true;
	}

	select_all.onclick = function() {
		for(let i = 0; i < departments.length; i++) {
			departments[i].checked = select_all.checked;
		}
	}

	departments.forEach(function(department) {
		department.onclick = function() {
			if(department.checked) {
				let all = true;
				for(let i = 0; i < departments.length; i++) {
					if(!departments[i].checked)
						all = false;
				}
				if(all)
					select_all.checked = true;
			} else {
				if(select_all.checked)
					select_all.checked = false;
			}
		}
	});

}
