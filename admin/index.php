<!doctype html>
<html lang="en-gb">

<head>
	<meta charset="utf-8" />
	
	<meta name="viewport" content="initial-scale=1.0, width=device-width, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	
	<title>Lando Admin</title>

	<link rel="icon" href="" />
	<link rel="apple-touch-icon" href="" />

	<link rel="stylesheet" href="css/master.css" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
	<script src="js/jquery.ui.touch-punch.min.js"></script>
	<script src="js/admin.js"></script>
	
</head>
<body>
<div id="wrapper">

<h1>Lando Admin</h1>

<form action="" method="post">
	<section>
		<h1>Site Details</h1>
		
		<div>
			<label for="site_title" class="field-label">Title</label>
			<input id="site_title" name="site_title" placeholder="Bespin Daily" />
		</div>
		
		<div>
			<label for="site_description" class="field-label">Description</label>
			<input id="site_description" name="site_description" placeholder="All the latest news from the cloud city." />
		</div>
		
		<div>
			<label for="site_root" class="field-label">Root URL</label>
			<input id="site_root" name="site_root" placeholder="http://" />
		</div>
		
		<div>
			<label for="pretty_urls">Remove index.php from URLs</label>
			<input id="pretty_urls" name="pretty_urls" type="checkbox" value="1" />
		</div>
	</section>
	
	<section>
		<h1>Content Source</h1>
		
		<div>
			<label for="host">Host</label>
			<select id="host" name="host">
				<option>Dropbox</option>
			</select>
		</div>
		
		<div>
			<label for="host_root" class="field-label">Path</label>
			<input id="host_root" name="host_root" value="/Public/Lando/Test Site" />
		</div>
	</section>
	
	<section>
		<h1>Theme Options</h1>
		<!-- <p>Download more themes from <a href="#">GitHub</a>.</p> -->
		
		<div>
			<label for="theme">Theme</label>
			<select id="theme" name="theme">
				<option>Default</option>
			</select>
		</div>
		
		<div>
			<label for="smartypants">Use nice punctuation (e.g. &ldquo;curly quotes&rdquo;)</label>
			<input id="smartypants" name="smartypants" type="checkbox" value="1" />
		</div>
	</section>

	<section id="page-nav">
		<h1>Page Navigation</h1>
		<p>Uncheck to hide from nav, drag to reorder</p>
		
		<input id="page_order" name="page_order" type="hidden" />
	
		<ol id="page-list" class="sortable">
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
	</section><!-- #page-nav -->
</form>

</div><!-- #wrapper -->
</body>
</html>