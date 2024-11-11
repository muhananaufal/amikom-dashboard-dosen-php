document.addEventListener('DOMContentLoaded', function () {
	const sidebar = document.querySelector('.sidebar');
	const toggler = document.querySelector('.sidebar-toggler');
	const content = document.querySelector('.content');

	toggler.addEventListener('click', function () {
		sidebar.classList.toggle('active');
		content.classList.toggle('active');
	});
});