<?php

return array(
  'frontendOptions' => array(
    'lifetime' => 2419200,
    'regexps' => array(
      '^/' => array(
        'cache' => true,
        'cache_with_session_variables' => true,
        'cache_with_cookie_variables' => true,
        'make_id_with_session_variables' => false,
        'make_id_with_cookie_variables' => false,
        'tags' => array(
          'posts', 'pages', 'categories'
        )
      ),
      '/publicada' => array(
        'cache' => false
      )
    ),
    'memorize_headers' => array(
      'Content-Encoding',
      'Content-Type'
    )
  ),
  'backendOptions' => array(
    'cache_dir' => APPLICATION_PATH . "/../writable/temp/"
  )
);