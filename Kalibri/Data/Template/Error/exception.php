<html>
<head>
	<title>Kalibri::Exception</title>
	<style type="text/css">
.linenum{
    text-align:right;
    background:#FDECE1;
    border:1px solid #cc6666;
    padding:0px 1px 0px 1px;
    font-family:Courier New, Courier;
    float:left;
    width:17px;
    margin:3px 0px 30px 0px;
    }

code    {/* safari/konq hack */
    font-family:Courier New, Courier;
}

.linetext{
    width:700px;
    text-align:left;
    background:white;
    border:1px solid #cc6666;
    border-left:0px;
    padding:0px 1px 0px 8px;
    font-family:Courier New, Courier;
    float:left;
    margin:3px 0px 30px 0px;
    }

br.clear    {
    clear:both;
}

</style>
</head>
<body>
	<h1>Exception</h1>
	<table>
	<tr>
		<td>Message:</td>
		<td><?php echo $ex->getMessage()?></td>
	</tr>
	<tr>
		<td>File</td>
		<td><?php echo $ex->getFile()?></td>
	</tr>
	<tr>
		<td>Line:</td>
		<td><?php echo $ex->getLine()?></td>
	</tr>
	
	<? if( $ex instanceof \Kalibri\Db\Exception ): ?>
	<tr>
		<td>Query:</td>
		<td><?=$ex->getQuery()?></td>
	</tr>
	<tr>
		<td>Query params:</td>
		<td><pre><? print_r( $ex->getQueryParams() )?></pre></td>
	</tr>
	<? endif ?>
	<tr>
		<td>Trace:</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2"><pre><?php echo $ex->getTraceAsString()?></pre></td>
	</tr>
	<tr>
		<td>Code:</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $code?></td>
	</tr>
	</table>
</body>
</html>