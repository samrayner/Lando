<!doctype html>
<html lang="en-gb">

<head>
	<meta charset="utf-8" />
	
	<title>Lando Admin</title>

	<link rel="stylesheet" href="css/master.css" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
	<script src="js/jquery.ui.touch-punch.min.js"></script>
	<script src="js/admin.js"></script>
	
</head>
<body>

	<h3>Page Navigation</h3>
	<p>Uncheck to hide from navigation. Drag to reorder.</p>
	
	<input id="page_order" name="page_order" type="hidden" />

	<ol id="page-nav" class="sortable">
		<li id="about-me">
			<div>
				<input id="about-me_visibility" name="about-me_visibility" type="checkbox" checked />
				<label for="about-me_visibility">About Me</label>
			</div>
		</li>
		<li id="contact">
			<div>
				<input id="contact_visibility" name="contact_visibility" type="checkbox" checked />
				<label for="contact_visibility">Contact</label>
			</div>
			<ol class="sortable">
				<li id="everyone">
					<div>
						<input id="everyone_visibility" name="everyone_visibility" type="checkbox" />
						<label for="everyone_visibility">Everyone</label>
					</div>
				</li>
				<li id="just-the-ceo">
					<div>
						<input id="just-the-ceo_visibility" name="just-the-ceo_visibility" type="checkbox" checked />
						<label for="just-the-ceo_visibility">Steve Jobs</label>
					</div>
				</li>
				<li id="just-me">
					<div>
						<input id="just-me_visibility" name="just-me_visibility" type="checkbox" />
						<label for="just-me_visibility">Webmaster</label>
					</div>
					<ol class="sortable">
						<li id="by-email">
							<div>
								<input id="by-email_visibility" name="by-email_visibility" type="checkbox" checked />
								<label for="by-email_visibility">Email Me</label>
							</div>
						</li>
						<li id="by-pigeon">
							<div>
								<input id="by-pigeon_visibility" name="by-pigeon_visibility" type="checkbox" />
								<label for="by-pigeon_visibility">Mmmm, cheese</label>
							</div>
						</li>
					</ol>
				</li>
			</ol>
		</li>
		<li id="home">
			<div>
				<input id="home_visibility" name="home_visibility" type="checkbox" checked />
				<label for="home_visibility">Home</label>
			</div>
		</li>
	</ol>

</body>
</html>