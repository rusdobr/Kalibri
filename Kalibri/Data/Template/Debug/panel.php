<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Scada&subset=latin,cyrillic"/>
<style type="text/css" type="text/css" media="screen, tv, projection">
	.debug-panel{position:fixed;bottom:0;right:0;background: #eee;font-family:Scada,Arial,Helvetica,sans-serif;}

/* remove all list stylings */
.debug-panel .menu, .debug-panel .menu ul {
	margin: 0;
	padding: 0;
	border: 0;
	list-style-type: none;
	display: block;
}

.debug-panel .menu li {
	margin: 0;
	padding: 0;
	border: 0;
	display: block;
	float: left;	/* move all main list items into one row, by floating them */
	position: relative;	/* position each LI, thus creating potential IE.win overlap problem */
	z-index: 5;		/* thus we need to apply explicit z-index here... */
}

.debug-panel .menu li:hover {
	z-index: 10000;	/* ...and here. this makes sure active item is always above anything else in the menu */
	white-space: normal;/* required to resolve IE7 :hover bug (z-index above is ignored if this is not present)
							see http://www.tanfa.co.uk/css/articles/pure-css-popups-bug.asp for other stuff that work */
}

.debug-panel .menu li li {
	float: none;/* items of the nested menus are kept on separate lines */
	padding:1px 5px;
}

.debug-panel .menu ul {
	visibility: hidden;	/* initially hide all submenus. */
	position: absolute;
	z-index: 10;
	left: 0;	/* while hidden, always keep them at the bottom left corner, */
	bottom: 0;		/* 		to avoid scrollbars as much as possible */
}

.debug-panel .menu li:hover>ul {
	visibility: visible;	/* display submenu them on hover */
	bottom: 100%;	/* 1st level go above their parent item */
}

.debug-panel .menu li li:hover>ul {	/* 2nd+ levels go on the right side of the parent item */
	bottom: 0;
	left: 100%;
}

/* -- float.clear --
	force containment of floated LIs inside of UL */
.debug-panel .menu:after, .menu ul:after {
	content: ".";
	height: 0;
	display: block;
	visibility: hidden;
	overflow: hidden;
	clear: both;
}
.debug-panel .menu, .debug-panel .menu ul {	/* IE7 float clear: */
	min-height: 0;
}
/* -- float.clear.END --  */

/* sticky submenu: it should not disappear when your mouse moves a bit outside the submenu
	YOU SHOULD NOT STYLE the background of the ".menu UL" or this feature may not work properly!
	if you do it, make sure you 110% know what you do */
.debug-panel .menu ul {
	padding: 30px 30px 10px 30px;
	margin: 0 0 -10px -30px;
}
.debug-panel .menu ul ul {
	padding: 30px 30px 30px 10px;
	margin: 0 0 -30px -10px;
}


/* - - - ADxMenu: DESIGN styles - - - */

.debug-panel .menu, .debug-panel .menu ul li {
	color: #eee;
	background: #234;
}

.debug-panel .menu a {
	text-decoration: none;
	color: #eee;
	/*padding: .4em 1em;*/
	padding-left:10px;
	display: block;
	position: relative;
}

.debug-panel .menu a:hover, .debug-panel .menu li:hover>a {
	color: #fc3;
}

.debug-panel .menu li li {	/* create borders around each item */
	border: 1px solid #ccc;
}
.debug-panel .menu ul>li + li {	/* and remove the top border on all but first item in the list */
	border-top: 0;
}

.debug-panel .menu li li:hover>ul {	/* inset 2nd+ submenus, to show off overlapping */
	bottom: 5px;
	left: 90%;
}

/* special colouring for "Main menu:", and for "xx submenu" items in ADxMenu
	placed here to clarify the terminology I use when referencing submenus in posts */
.debug-panel .menu>li:first-child>a, .debug-panel .menu li + li + li li:first-child>a {
	color: #567;
}

.debug-panel h4{margin:0;padding:0}
</style>

<div class="debug-panel">
	<ul class="menu">
		
		<li><a>Kalibri</a></li>
		<li>
			<a class="group">Includes</a>
			<ul class="sub" id="info-includes">
				<?php foreach( $marks[ \Kalibri\Benchmark::RESULTS_MARK ]['includes'] as $include ): ?>
				<li><?php echo $include?></li>
				<?php endforeach ?>
			</ul>
		</li>
		<li>
			<a class="group">Classes</a>
			<ul class="sub" id="info-classes">
				<?php $started = false; foreach( get_declared_classes() as $class ):?>
					<?php if( !$started && $class !== 'Kalibri' ) {
						continue;
					} else {
						$started = true;
					}
					?>
				<li><?php echo $class?></li>
				<?php endforeach ?>
			</ul>
		</li>
		<li>
			<a class="group">Marks</a>
			<ul class="sub" id="info-marks">
				<?php foreach( $marks as $mark=>$data ): if( $mark == \Kalibri\Benchmark::RESULTS_MARK ){ continue; }?>
				<li><h4><?php echo $mark?></h4>
					<div style="min-width:150px;font-size:smaller;">
						<div><span class="record">Duration:</span> <?php echo number_format( $data['execution'], 4)?> s</div>
						<div><span class="record">Memory:</span> <?php echo number_format( $data['memory_used'] / 1024/1024, 2)?> Mb</div>
						<div><span class="record">Starts:</span> <?php echo $data['starts']?></div>
						<div><span class="record">Start offset:</span> <?php echo $data['start_offset']?></div>
						<?php// var_dump( $data );?>
					</div>
				</li>
				<?php endforeach ?>
			</ul>
		</li>
		<li><a><?php echo number_format( $marks['kalibri-total']['execution'], 4) ?>s</a></li>
		<li><a><?php echo number_format( $marks[ \Kalibri\Benchmark::RESULTS_MARK ]['memory'] / 1024 / 1024, 2).' / '.  number_format($marks[ \Kalibri\Benchmark::RESULTS_MARK ]['peak_memory'] / 1024/1024, 2)?> Mb</a></li>
		
		<!--li>DB</li-->
		<!--li>Cache</li-->
	</ul>
</div>
<script>
	function showGroup( objName ) {
		document.getElementById( objName ).style.display = 'block';
	}
</script>