<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "tailwind_styled_content".
 *
 * Auto generated 19-02-2025 08:25
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Tailwind Styled Content',
  'description' => 'Alternative for fluid_styled_content using Tailwind CSS, providing a clean, robust and modern starting point.',
  'category' => 'templates',
  'version' => '1.2.1',
  'state' => 'beta',
  'uploadfolder' => false,
  'clearcacheonload' => false,
  'author' => 'Joost Ramke',
  'author_email' => 'hey@joostramke.com',
  'author_company' => NULL,
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '11.5.0-13.9.99',
      'fluid' => '11.5.0-13.9.99',
      'frontend' => '11.5.0-13.9.99',
    ),
    'conflicts' => 
    array (
      'css_styled_content' => '*',
      'fluid_styled_content' => '*',
    ),
    'suggests' => 
    array (
    ),
  ),
);

