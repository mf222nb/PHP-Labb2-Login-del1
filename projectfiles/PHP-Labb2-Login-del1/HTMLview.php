<?php
/**
 * Created by PhpStorm.
 * User: Lowe
 * Date: 2014-09-15
 * Time: 17:47
 */
class HTMLview{
    public function presentPage($content){


        echo "
                <!DOCTYPE html>
                <html>
                <head>
                <meta charset=\"utf-8\" />
                <title>Labb2</title>
                </head>
                <body>
                    $content
                    <p style='bottom: 0; position: absolute; color: red; font-weight: bold;'>Obs, sidan använder cookies, genom att använda applikationen godkänner du cookies.</p>
                </body>
                </html>

            ";
    }


}