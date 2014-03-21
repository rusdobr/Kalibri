<!DOCTYPE html>
<html>
<head>
	<title>Kalibri::Exception</title>
    <style type="text/css">
        html, body{margin:0;padding: 0;width:100%;height:100%;background:#efefef;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;font-size:15px;}
        body{
            max-width: 780px;
            width:100%;
            margin: 15px auto;
            border:1px solid #ccc;
            background: #fff;
            border-radius: 10px;
            padding:15px;
            height:auto;
        }

        .no-args{color:#cacaca;}
        ul{list-style: none;margin:0;padding:0}
        ul ul{margin-left:20px;}
        /*.function .name{color:#428bca}*/
    </style>
</head>
<body>
    <? if( $ex instanceof \Kalibri\Db\Exception ): ?>
        <h1>Database error</h1>

        <table>
        <tr>
            <td>Message:</td>
            <td><?php echo $ex->getMessage()?></td>
        </tr>
        <tr>
            <td>Query:</td>
            <td><?=$ex->getQuery()?></td>
        </tr>
        <? if( count( $ex->getQueryParams() ) ): ?>
            <tr>
                <td>Query params:</td>
                <td><pre><?=printParams( $ex->getQueryParams() )?></pre></td>
            </tr>
        <? endif ?>
        </table>
    <? else: ?>
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
        <tr>
            <td>Code:</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><?php echo $code?></td>
        </tr>
        </table>
    <? endif ?>

    <h2>Stack trace</h2>
    <ul class="trace">
        <? $idx = count( $ex->getTrace() )+1; foreach( $ex->getTrace() as $key=>$line ): $idx--;?>
            <li>
                <div class="function"><?=$idx?>. <span class="name"><?=(isset( $line['class'] )? $line['class'].$line['type']: '').$line['function']?></span></div>
                <ul class="call">
                    <? if( is_array( $line['args'] ) && count( $line['args'] ) ): ?>
                        <? foreach( $line['args'] as $arg ): ?>
                            <li>
                                <? if( !is_array($arg) && !is_object( $arg ) ): ?>
                                    <?=$arg?>
                                <? elseif( is_array( $arg ) ): ?>
                                    <?=printParams( $arg )?>
                                <? else: ?>
                                    <pre>
                                        <? print_r( $arg )?>
                                    </pre>
                                <? endif?>
                            </li>
                        <? endforeach?>
                    <? else: ?>
                        <li class="no-args">No arguments</li>
                    <? endif ?>
                </ul>
            </li>
        <? endforeach ?>
        <li>0. {main}</li>
    </ul>
</body>
</html>
<?
function printParams( $arr )
{
    $result = '<ul>';

    foreach( $arr as $key=>$value )
    {
        if( is_object( $value ) )
        {
            $result .= "<li><span>".get_class( $value )."</span></li>";
        }
        else
        {
            $result .= "<li>".(!is_numeric( $key )? "<span>$key</span> =":''). $value."</li>";
        }
    }

    return $result .= '</ul>';
}