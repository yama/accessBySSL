<?php
/**
 * accessBySSL
 * version 1.0
 */
 
class accessBySSL {
  
  var $ids = array();
  var $append;
  var $full;

  function accessBySSL($ids = '', $append = '') {
    $this->ids = explode(',', $ids);
    $this->append = trim($append,'/');
  }

  function process() {
    global $modx;

    $output = &$modx->documentOutput;
    $host_name = substr($modx->config['site_url'],strpos($modx->config['site_url'],'://')+3);
    $host_name = rtrim($host_name,'/');
    $http_site_url = str_replace('https://','http://',$modx->config['site_url']);

    foreach ($this->ids as $id) {
      $link = $modx->makeUrl($id, '', '','full'); // Since 1.0.12J
      if(!$link) continue;
      $base_url = trim($modx->config['base_url'],'/');
      $link = $base_url . str_replace($modx->config['site_url'],'',$link);
      $output = preg_replace("@(https?://{$host_name})?/?{$link}@", "{$this->append}/{$link}", $output);
    }

    if (in_array($modx->documentIdentifier, $this->ids)) {
      $output = preg_replace('/<base href=("|\')(.+)\1/', '<base href="' . $this->append . '/"', $output);
      if(strpos($output,'/manager/')!==false) $output = str_replace('/manager/', 'manager/', $output);
      $output = preg_replace('@<a\s+href=("|\')(?!http)/(.+?)\1@im', '<a href="' . $http_site_url . '$2"', $output);
      $output = preg_replace('@<a\s+href=("|\')(?!http)(.+?)\1@im' , '<a href="' . $http_site_url . '$2"', $output);
      $output = preg_replace('@<a\s+href=("|\')https://' . $host_name . '(/?)\1@im' , '<a href="http://' . $host_name . '$2"', $output);
    }
    
  }
}
