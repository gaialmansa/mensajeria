<?php
/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */

/**
 * @var User $_user La instancia del usuario actual;
 */

use zfx\Config;
use function zfx\a;
use function zfx\av;
use function zfx\trueEmpty;


if (isset($_user)) {
    $_user->getPermissions(TRUE);
}

if (isset($_hypercode)) {

    ?>
    <div class="_adm_menuGroup">
        <?php
        if (a($_hypercode, 'action')) {
            echo '<form method="post" action="' . a($_hypercode, 'action') . '">';
        }
        echo a($_hypercode, $_lang);
        echo '<input class="_adm_menuHypercode" type="text" ' .
             (a($_hypercode, 'name') ? 'name="' . a($_hypercode, 'name') . '" ' : '') .
             (a($_hypercode, 'id') ? 'id="' . a($_hypercode, 'id') . '" ' : '') .
             (a($_hypercode, 'size') ? 'size="' . (int)a($_hypercode, 'size') . '" ' : 'size="16"') .
             (a($_hypercode, 'maxlength') ? 'maxlength="' . (int)a($_hypercode,
                     'maxlength') . '" ' : 'maxlength="40"') . '>';
        if (a($_hypercode, 'action')) {
            echo "</form>";
        }

        ?>
    </div>
    <?php
}

?>

<?php
if ($_groups) {

    ?>
    <?php
    foreach ($_groups as $g) {
        if (isset($_user) && av($g, 'perm') && !$_user->checkMenuPermission($g['perm'])) {
            continue;
        }

        ?>
        <div class="_adm_menuGroup">
            <div class="_header"><?php echo $g[$_lang][1]; ?><?php echo $g[$_lang][0]; ?></div>
            <?php
            $sections = a($g, 'sections');
            if ($sections) {

                ?>
                <div class="_adm_menuSections">
                    <?php
                    $currentSection = NULL;
                    foreach ($sections as $id => $section) {
                        if (isset($_user) && av($section, 'perm') && !$_user->checkMenuPermission($section['perm'])) {
                            continue;
                        }

                        if ($id == $_csec) {
                            $classCode      = 'class="_adm_menuSelected" ';
                            $currentSection = $section;
                        }
                        else {
                            $classCode = '';
                        }
                        $_firstSection = a($section, 'defaultSubSection');
                        if (a($section, 'url')) {
                            $_url = a($section, 'url');
                        }
                        else {
                            if (a($section, 'controller')) {
                                $_url = Config::get('rootUrl') . (!trueEmpty(a($section, 'module')) ? a($section, 'module') . '/' : '') . $section['controller'] . ($section['controller'] ? ('/' . $_firstSection) : '');
                            }
                            else {
                                $_url = 'javascript:void(0)';
                            }
                        }
                        if (a($section, 'target')) {
                            $_target = ' target="' . a($section, 'target') . '" ';
                        }
                        else {
                            $_target = '';
                        }

                        ?>
                        <a <?php echo $classCode; ?> <?php echo $_target; ?> href="<?php echo $_url; ?>">
                            <?php echo $section[$_lang][1]; ?><?php echo $section[$_lang][0]; ?>
                        </a>
                        <?php
                        $subsections = a($section, 'subsections');
                        if ($subsections) {

                            ?>
                            <div class="_adm_menuSubsections">
                                <?php
                                foreach ($subsections as $id => $subsection) {
                                    if (isset($_user) && av($subsection, 'perm') && !$_user->checkMenuPermission($subsection['perm'])) {
                                        continue;
                                    }
                                    if (isset($_cssec) && $id == $_cssec) {
                                        $classCode = 'class="_adm_menuSelected" ';
                                    }
                                    else {
                                        $classCode = '';
                                    }
                                    if (!a($subsection, 'url')) {
                                        if (av($subsection, 'controller')) {
                                            $url = Config::get('rootUrl') . (!trueEmpty(a($subsection, 'module')) ? a($subsection, 'module') . '/' : '') . $subsection['controller'] . '/';
                                        }
                                        elseif (av($subsection, 'subcontroller')) {
                                            $url = Config::get('rootUrl') . $subsection['subcontroller'] . '/' . $id;
                                        }
                                        else {
                                            $url = Config::get('rootUrl') . (!trueEmpty(a($currentSection, 'module')) ? a($currentSection, 'module') . '/' : '') . $currentSection['controller'] . '/' . $id;
                                        }
                                    }
                                    else {
                                        $url = $subsection['url'];
                                    }
                                    if (a($subsection, 'target')) {
                                        $_target = ' target="' . a($subsection, 'target') . '" ';
                                    }
                                    else {
                                        $_target = '';
                                    }

                                    ?>
                                    <a <?php echo $classCode; ?> <?php echo $_target; ?>
                                            href="<?php echo $url; ?>"><?php echo $subsection[$_lang][1]; ?><?php echo $subsection[$_lang][0]; ?></a>
                                    <?php
                                }
                                ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php
} /* if $_groups */
