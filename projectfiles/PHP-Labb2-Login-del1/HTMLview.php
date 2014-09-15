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
                </head>
                <body>
                    $content
                </body>
                </html>

            ";
    }


}