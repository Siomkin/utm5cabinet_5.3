<?php
class DRG_Controller_Action_Helper_LayoutLoader extends Zend_Controller_Action_Helper_Abstract
{

    public function preDispatch()
    {
        $bootstrap = $this->getActionController()->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        $module = $this->getRequest()->getModuleName();
        if (isset ($config [$module] ['resources'] ['layout'] ['layout'])) {
            $layoutScript = $config [$module] ['resources'] ['layout'] ['layout'];
            $this->getActionController()->getHelper('layout')->setLayout($layoutScript);
        }
    }

    public function postDispatch()
    {

        /*

          $bootstrap = $this->getActionController ()->getInvokeArg ( 'bootstrap' );
          $config = $bootstrap->getOptions ();

          $params ['profiler'] = $config->db->profiler;

          $db = Zend_Db::factory ( 'PDO_MYSQL', $params );

          $profiler = $db->getProfiler();

          $totalTime = $profiler->getTotalElapsedSecs ();
          $queryCount = $profiler->getTotalNumQueries ();
          $longestTime = 0;
          $longestQuery = null;

          foreach ( $profiler->getQueryProfiles () as $query ) {
              if ($query->getElapsedSecs () > $longestTime) {

                  $longestTime = $query->getElapsedSecs ();

                  $longestQuery = $query->getQuery ();

              }

          }

          echo 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "\n";
          echo 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "\n";
          echo 'Queries per second: ' . $queryCount / $totalTime . "\n";
          echo 'Longest query length: ' . $longestTime . "\n";
          echo "Longest query: \n" . $longestQuery . "\n";
          */
    }

}
