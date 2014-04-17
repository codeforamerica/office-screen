<?php

    function format_week($time)
    {
        return sprintf('%s-W%s', date('Y', $time), date('W', $time));
    }
    
    function yesno($value)
    {
        if(is_null($value))
            return 'No';
        
        if($value > 1000)
            return sprintf('%.1fk', $value / 1000);
        
        return $value;
    }

    // GDocs spreadsheet with current kitchen data.
    $csv_url = 'https://docs.google.com/a/codeforamerica.org/spreadsheets/d/1XvhA5LC2BBVtgxFVwR0RkX3bbV3nT-fjtyyrd_uOFzg/export?format=csv&id=1XvhA5LC2BBVtgxFVwR0RkX3bbV3nT-fjtyyrd_uOFzg&gid=0';
    
    date_default_timezone_set('America/Los_Angeles');
    
    $now = format_week(time());
    $values = array();
    $weeks = array();

    if($fp = @fopen($csv_url, 'r'))
    {
        // Iterate over each row of the CSV file, with row number in $row.
        for($row = 0; $row_cells = fgetcsv($fp); $row++)
        {
            $row_label = $row_cells[0];

            if($row == 0 && $row_label != 'Data Point') {
                // Bail out if cell [0, 0] doesn't say "Data Point"
                exit(1);

            } elseif($row == 1) {
                // Make an array for each week, in reverse-chronological order.
                for($v = 1; $v < count($row_cells); $v++)
                {
                    $week = array(
                        'label' => $row_cells[$v],
                        'week' => format_week(strtotime($row_cells[$v])),
                        'values' => array()
                        );
                    
                    if($week['week'] <= $now)
                        array_unshift($weeks, $week);
                }
            
            } elseif($row >= 2) {
                // Populate values for each week.
                for($c = 1, $w = count($weeks) - 1; $c < count($row_cells); $c++, $w--)
                {
                    if($weeks[$w])
                    {
                        $weeks[$w]['values'][$row_label] = $row_cells[$c];
                        $values[$row_label] = floatval($values[$row_label]) + floatval($row_cells[$c]);
                    }
                }
            }
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <!--link rel="stylesheet" href="http://style.codeforamerica.org/0/style/css/main.css">
        <link rel="stylesheet" href="http://style.codeforamerica.org/0/style/css/layout.css" media="all and (min-width: 40em)">
        <link href="http://style.codeforamerica.org/0/style/css/prism.css" rel="stylesheet" /-->
        <link href="style/main.css" rel="stylesheet" />
        <title>CFA Dashboard</title>
    </head>    
    <body>
        <h1>Code for America - Kitchen Stories <span>Last updated on <?= date('F jS Y, g:ia') ?></h1>
        <!--nav class="nav-tabs" role="navigation">
            <ul class="layout-semibreve layout-tight">
                <li class="nav-tab"><a class="nav-tab-link" href="edit.html">English</a></li>
                <li class="nav-tab"><a class="nav-tab-link" href="#">Spanish</a></li>
                <li class="nav-tab"><a class="nav-tab-link" href="#">Chinese</a></li>
            </ul>
        </nav-->
        <!--ul class="detailed-info">
            <li><a href="">Stats</a></li>
            <li><a href="history.html">History</a></li>
        </ul-->

        <table width="100%" cellpadding=0 cellspacing=0 border=0>
        <tr>
            <td class="bigstats fruit">
                <ul>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/7.png')"><?= yesno($values['# of bananas consumed']) ?></span> bananas eaten</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/35.png')"><?= yesno($values['Pounds of Hot Tamales consumed']) ?></span> pounds of Hot Tomales chomped</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/11.png')"><?= yesno($values['# of oranges consumed']) ?></span> oranges squeezed</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/16.png')"><?= yesno($values['# of pears consumed']) ?></span> pears paired</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/4.png')"><?= yesno($values['Pounds of grapes consumed']) ?></span> pounds of grapes plucked</li>
                </ul>
            </td>
            
            <td class="bigstats beverages">
                <ul>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/24.png')"><?= yesno($values['Pounds of strawberries consumed']) ?></span> pounds of strawberries scoffed</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/2.png');"><?= yesno($values['Pounds of apples consumed']) ?></span> pounds of apples eaten</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/57.png')"><?= yesno($values['# of oranges consumed']) ?></span> string cheeses unstrung</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/173.png')"><?= yesno($values['# of cups of regular coffee brewed']) ?></span> cups of coffee made</li>
                </ul>
            </td>

            <td class="bigstats office">
                <ul>

                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/196.png')"><?= yesno($values['# of gallons of milk (2%) consumed']) ?></span> gallons of milk consumed</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/173.png')"><?= yesno($values['# of cups of decaf coffee brewed']) ?></span> cups of decaf made</li>
                    <li><span class="value"><?= yesno($values['# of Instacart deliveries']) ?></span> InstaCart deliveries</li>
                    <li><span class="value"><?= yesno($values['# of times dishwasher is cycled']) ?></span> dishwasher cycles</li>
                    <li><span class="value" style="background-image:url('style/food-icons/png/64 px/175.png')"><?= yesno($values['# of CfA catered meals']) ?></span> CfA catered meals</li>
                </ul>
            </td>
        </tr>
       
        <tr>
            <td class="warzone" colspan="3">
                <h2>Days kitchen has been declared a disaster zone:</h2>
                <p>14</p><!--# of times kitchen looks like a declared disaster zone (M-F only)-->
            </td>
        </tr>

        </table>

     
        
        

    
    </body>
</html>