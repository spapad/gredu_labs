<?php
/**
 * gredu_labs.
 *
 * @link https://github.com/eellak/gredu_labs for the canonical source repository
 *
 * @copyright Copyright (c) 2008-2015 Greek Free/Open Source Software Society (https://gfoss.ellak.gr/)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */
namespace GrEduLabs\TeacherForm\Service;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;
use GrEduLabs\UniversityForm\Service\UniversityFormService;

class TeacherFormService implements TeacherFormServiceInterface
{

    public function submit(array $data)
    {
        $appForm = R::dispense('volunteerteachers');
//        $appForm->id                  = $data['id'];
        $appForm->name = $data['name'];
        $appForm->surname = $data['surname'];
        $appForm->eidikothta = $data['eidikothta'];
        $appForm->arithmitroou = $data['arithmitroou'];
        $appForm->email = $data['email'];
        $appForm->telef = trim($data['telef']);
        $appForm->school = $data['school'];
        $appForm->schooltelef = trim($data['schooltelef']);
        $appForm->projectdescription = $data['projectdescription'];
        $appForm->comments = $data['comments'];
        R::store($appForm);
        return $appForm;
    }

    public function getBranches()
    {
        return array_map(function ($branch) {
            return $branch->export();
        }, R::findAll('branch', 'ORDER BY name ASC'));
    }

    /**
     * 
     * @param OODBBean $bean the form bean
     * @return array
     */
    private function exportApplicationForm(OODBBean $bean)
    {
        $form = $bean->export();

        $form['projectdescription'] = UniversityFormService::recomposeProjects($form['projectdescription']);

        return $form;
    }

    /**
     * Get all the university volunteer submissions 
     * 
     * @return array The exported bean info from retrieved data
     */
    public function findAll()
    {
        $forms = R::getAll('SELECT `volunteerteachers`.*, `branch`.`name` AS `eidikothta_name` '
                . 'FROM `volunteerteachers` JOIN `branch` ON (`volunteerteachers`.`eidikothta` = `branch`.`id`) '
                . 'ORDER BY `id` DESC');
        $beanForms = R::convertToBeans('volunteerteachers', $forms);

        return array_map(function ($form) {
            return $this->exportApplicationForm($form);
        }, $beanForms);
    }
}
