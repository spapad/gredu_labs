<?php
/**
 * gredu_labs.
 *
 * @link https://github.com/eellak/gredu_labs for the canonical source repository
 *
 * @copyright Copyright (c) 2008-2015 Greek Free/Open Source Software Society (https://gfoss.ellak.gr/)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */
namespace GrEduLabs\UniversityForm\Service;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class UniversityFormService implements UniversityFormServiceInterface
{

    const PLAIN_TEXT_SEPARATOR = ' ---------------NEXT_PROJECT--------------- ';

    public function submit(array $data)
    {
        $appForm = R::dispense('univ');
        $appForm->id = $data['id'];
        $appForm->idrima = $data['idrima'];
        $appForm->sxolh = $data['sxolh'];
        $appForm->tmhma = $data['tmhma'];
        $appForm->ereunitiko = $data['ereunitiko'];
        $appForm->institute = $data['institute'];
        $appForm->other = $data['other'];
        $appForm->erga = $data['erga'];
        $appForm->person = $data['person'];
        $appForm->telef = trim($data['telef']);
        $appForm->email = $data['email'];
        $appForm->projectdescription = $data['projectdescription'];
        $appForm->comments = $data['comments'];

        R::store($appForm);
        return $appForm;
    }

    public static function recomposeProjects($data, $html = true)
    {
        $recons = [];
        if (($projects = preg_split('/###@@@###/msUu', $data)) !== false) {
            $projects_parts = array_map(function ($r) {
                return preg_split('/_@@@_/msUu', $r);
            }, $projects);
            $projects_cnt = 0;
            foreach ($projects_parts as $part) {
                if (count($part) > $projects_cnt) {
                    $projects_cnt = count($part);
                }
            }
            $recons = array_fill(0, $projects_cnt, '');
            for ($i = 0; $i < $projects_cnt; $i++) {
                $recons[$i] .= $projects_parts[0][$i] . "\r\n"
                    . (isset($projects_parts[1][$i]) ? $projects_parts[1][$i] : '') . "\r\n"
                    . (isset($projects_parts[2][$i]) ? $projects_parts[2][$i] : '');
            }
            $recons = array_filter($recons, function ($v) {
                return trim($v) != '';
            });
        }
        if (count($recons) > 0) {
            if ($html === true) {
                $data = '<ol><li>' . implode("</li>\n<li>", $recons) . '</li></ol>';
            } else {
                $data = implode(self::PLAIN_TEXT_SEPARATOR, $recons);
            }
        } else {
            $data = '-';
        }
        return $data;
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
        $forms = R::getAll('SELECT univ.* '
                . 'FROM univ '
                . 'ORDER BY id DESC');
        $beanForms = R::convertToBeans('univ', $forms);

        return array_map(function ($form) {
            return $this->exportApplicationForm($form);
        }, $beanForms);
    }
}
