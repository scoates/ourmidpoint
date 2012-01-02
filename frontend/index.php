<?php
require __DIR__ . '/../lib.php';
$input = array(
	'f' => isset($_GET['f']) ? $_GET['f'] : '',
	't' => isset($_GET['t']) ? $_GET['t'] : '',
	'd' => isset($_GET['d']) ? $_GET['d'] : date('m/d/Y', time() + 30 * 24 * 60 * 60),
);
$count = 0;
$safe = array_map(function ($el) use (&$count) {
	if ($el) {
		++$count;
	}
	return htmlentities($el, ENT_QUOTES, 'UTF-8');
}, $input);

if (count($input) == $count) {
	// success
	$dep = time() + (30 * 24 * 60 * 60);
	$ret = $dep + (7 * 24 * 60 * 60);

	$result = fetch($input['f'], $input['t'], $dep, $ret);
	//$result = file_get_contents('out.xml');
	//echo '<pre> ' . htmlentities($result) . ' </pre>';
	$xml = simplexml_load_string($result);
	$parsed = parse($xml, $input['f'], $input['t']);
} else {
	$parsed = null;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<script src="https://www.google.com/jsapi" charset="UTF-8"></script>
		<script type="text/javascript" charset="UTF-8">google.load("jquery", "1.7.1");</script>
		<script type="text/javascript" charset="UTF-8">google.load("jqueryui", "1.8.16");</script>
		<link href="css/main.css" media="all" rel="stylesheet" type="text/css" />
		<title>Where should we meet?</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="initial-scale=1.0, width=device-width" />
		</style>
	</head>
	<body>
		<div id="wrap" class="container">
			<div id="box">
				<div id="box-content">
					<form action="./" method="GET">
						I will be travelling from:
						<input type="text" class="airport" name="f" value="<?=$safe['f']?>"/>
						and I would like to visit a friend who will be travelling from
						<input type="text" class="airport" name="t" value="<?=$safe['t']?>"/> on
						<input type="text" class="date" name="d" value="<?=$safe['d']?>"/>…
						<input type="submit" value="Where should we meet?"/>
					</form>

					<?php
					if ($parsed) {
						$intersect = array_intersect(
							$parsed['left']['directAirports'],
							$parsed['right']['directAirports']
						);
						if ($intersect) {
							echo "You should meet in: " . implode(', ', $intersect);
						} else {
							echo "No cities found. )-:";
						}
					}
					?>
				</div>
			</div>
		</div>
<script type="text/javascript">
	$("input.airport").autocomplete({
		source: 'airports.php',
		position: {
			my: "top",
			at: "bottom",
		}
	});
	$(function() {
		$("input.date").datepicker();
	});
</script>
	</body>
</html>
