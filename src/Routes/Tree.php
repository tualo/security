<?php

namespace Tualo\Office\Security\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\FiskalyAPI\API;

use Tualo\Office\Basic\Session;
use Tualo\Office\Basic\MYSQL\Database;

class Tree implements IRoute
{
    public static function register()
    {
        Route::add('/security/tree', function ($matches) {
            TualoApplication::contenttype('application/json');
            try {
                $rx = [];
                $routes = Route::getRoutes();
                foreach ($routes as $route) {
                    if (isset($route['needActiveSession']) && ($route['needActiveSession'] === false)) {
                        $rx[] = $route;
                    }
                }


                TualoApplication::result('routes', $rx);
            } catch (\Exception $e) {
                TualoApplication::result('msg', $e->getMessage());
            }
        }, ['get'], true);


        Route::add('/security/test', function ($matches) {
            TualoApplication::contenttype('application/json');
            $db = TualoApplication::get('session')->getDB();
            try {

                // mysql -h fpcluster02.tualo.io -u doc_fb -pBgktcbh6gfz

                $target_row = [

                    'db_user' => 'doc_fb',
                    'db_pass' => 'Bgktcbh6gfz',
                    'db_name' => 'fb_wvd',
                    'db_host' => 'fpcluster02.tualo.io',
                    'db_port' => 3306
                ];
                $target = new Database($target_row['db_user'], $target_row['db_pass'], $target_row['db_name'], $target_row['db_host'], intval($target_row['db_port']), $target_row['key_file'] ?? null, $target_row['cert_file'] ?? null, $target_row['ca_file'] ?? null);

                $target_ids = $target->direct("select document_link from fb_wvd.doc_binary ", [], 'document_link');

                //$target = Session::newDBByRow($target_row);
                $sql = 'select document_link, create_date from fb_wvd.doc_binary order by create_date desc';
                $insert_count = 0;
                $source_list = $db->direct($sql);

                ini_set('memory_limit', '512M');
                error_reporting(E_ALL);
                ini_set('display_errors', '1');

                foreach ($source_list as $crow) {
                    if (!isset($target_ids[$crow['document_link']])) {

                        $row = $db->singleRow('select * from fb_wvd.doc_binary where document_link = {document_link}', ['document_link' => $crow['document_link']]);
                        $base64 = base64_encode($row['doc_data']);




                        $sql = '
                            INSERT INTO doc_binary (document_link, doc_data, last_upd_user, last_upd_date, create_user, create_date, archive) VALUES 
                            (' . intval($row['document_link']) . ', FROM_BASE64(\'' . $target->escape_string($base64) . '\'), \'' . $target->escape_string($row['last_upd_user']) . '\',\'' . $target->escape_string($row['last_upd_date']) . '\',' . intval($row['create_user']) . ', \'' . $target->escape_string($row['create_date']) . '\', ' . intval($row['archive']) . ')
                            on duplicate key update
                            doc_data=VALUES(doc_data), last_upd_user=VALUES(last_upd_user), last_upd_date=VALUES(last_upd_date), archive=VALUES(archive);
                        ';
                        $target->direct($sql);




                        $insert_count++;
                    }
                    if ($insert_count > 50) {
                        TualoApplication::result('insert_count', $insert_count);
                        break;
                    }
                }





                TualoApplication::result('success', true);
                TualoApplication::result('insert_count', $insert_count);
                // TualoApplication::result('in_array', in_array(182, $target_ids));
            } catch (\Exception $e) {
                TualoApplication::result('msg_sql', $target->last_sql);
                TualoApplication::result('msg', $e->getMessage());
            }
        }, ['get'], true);
    }
}
