<?php
/**
 * gredu_labs.
 *
 * @link https://github.com/eellak/gredu_labs for the canonical source repository
 *
 * @copyright Copyright (c) 2008-2015 Greek Free/Open Source Software Society (https://gfoss.ellak.gr/)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */
namespace GrEduLabs\TeacherForm\Action;

use GrEduLabs\TeacherForm\Service\TeacherFormServiceInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class Display
{

    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var FormServiceInterface
     */
    protected $formService;

    public function __construct(Twig $view, TeacherFormServiceInterface $formService)
    {
        $this->view = $view;
        $this->formService = $formService;
    }

    public function __invoke(Request $req, Response $res)
    {
        $forms = $this->formService->findAll();
        return $this->view->render($res, 'teacher_form/display.twig', [
                'forms' => $forms
        ]);
    }
}
