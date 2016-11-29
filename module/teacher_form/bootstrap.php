<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * gredu_labs.
 *
 * @link https://github.com/eellak/gredu_labs for the canonical source repository
 *
 * @copyright Copyright (c) 2008-2015 Greek Free/Open Source Software Society (https://gfoss.ellak.gr/)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

return function (App $app) {
    $container = $app->getContainer();
    $events    = $container['events'];

    $events('on', 'app.autoload', function ($autoloader) {
        $autoloader->addPsr4('GrEduLabs\\TeacherForm\\', __DIR__ . '/src/');
    });

    $events('on', 'app.services', function ($container) {
        $container[GrEduLabs\TeacherForm\Service\TeacherFormServiceInterface::class] = function ($c) {
            return new GrEduLabs\TeacherForm\Service\TeacherFormService();
        };

        $container[GrEduLabs\TeacherForm\InputFilter\TeacherForm::class] = function ($c) {
            return new GrEduLabs\TeacherForm\InputFilter\TeacherForm();
        };


        $container[GrEduLabs\TeacherForm\Action\TeacherForm::class] = function ($c) {
            return new GrEduLabs\TeacherForm\Action\TeacherForm(
                $c->get('view'),
                $c->get(GrEduLabs\TeacherForm\Service\TeacherFormServiceInterface::class),
                $c->get(GrEduLabs\TeacherForm\InputFilter\TeacherForm::class),
                $c->get('router')->pathFor('teacher_form.submit_success'),
                $c);
        };

        $container[GrEduLabs\TeacherForm\Action\SubmitSuccess::class] = function ($c) {
            return new GrEduLabs\TeacherForm\Action\SubmitSuccess(
                $c->get('view'),
                $c->get('router')->pathFor('teacher_form')
            );
        };
    });


    $events('on', 'app.bootstrap', function ($app, $container) {
        $container['view']->getEnvironment()->getLoader()->prependPath(__DIR__ . '/templates');

        $app->group('/teacher-form', function () {
            $this->map(['get', 'post'], '', GrEduLabs\TeacherForm\Action\TeacherForm::class)
                ->add(GrEduLabs\Application\Middleware\AddCsrfToView::class)
                ->add('csrf')
                ->setName('teacher_form');
            $this->get('/submit-success', GrEduLabs\TeacherForm\Action\SubmitSuccess::class)
                ->setName('teacher_form.submit_success');
        });
        $app->get('/teacher-form/mm', function (Request $req, Response $res) use ($container) {
            $school_name = $req->getQueryParam('term');

            $httpClient = new GuzzleHttp\Client([
                    'base_uri' => $container['settings']['sch_mm']['public_api_url']
                    ]);

            $config   = $httpClient->getConfig();
            $baseUri  = $config['base_uri'];
            $url      = $baseUri . "?name=" . $school_name;
            $response = $httpClient->request('GET', $url);

            $responseData = json_decode($response->getBody()->getContents(), true);
            if (!isset($responseData['data']) || empty($responseData['data'])) {
                return;
            }
            $cnt = count($responseData['data']);
            $school_arr = [];
            for ($i=0; $i<$cnt; $i++) {
                $school_arr[$i]['mm_id'] = $responseData['data'][$i]['mm_id'];
                $school_arr[$i]['value'] = $responseData['data'][$i]['name'];
                $school_arr[$i]['tel'] = $responseData['data'][$i]['phone_number'];
            }

            return $res->withJson($school_arr);
        })->setName('teacher_form.mm');
    });
};
