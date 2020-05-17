	var uphand =document.getElementById('up');
	uphand.style.animationPlayState = 'running';

	var lefthand =document.getElementById('left');

	var subjects = document.querySelector('.collapse a');

	var button = document.getElementById('tutorial');
	button.style.border='4px solid red';

	button.addEventListener('click',function(){

	if(subjects!==null)
	{
		lefthand.style.display='block';
		subjects.style.padding="3px";
		subjects.style.border = '4px solid green';
	
	lefthand.style.animationPlayState = 'running';
	var x = document.getElementById('helptext');
	x.innerHTML = 'Click on any subject to read about it';
	x.style.color='green';
	setTimeout(function(){
		lefthand.style.display='none';
		subjects.style.border = '';
		subjects.style.padding="";
		x.innerHTML = '';
	},4000);
	}

	})

	setTimeout(function(){
		uphand.remove();
		button.style.border = '';
	},4000);

// <link rel="stylesheet" href="manageContent.css">	