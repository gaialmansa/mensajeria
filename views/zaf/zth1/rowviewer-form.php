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

use zfx\StrFilter;
use function zfx\a;
use function zfx\trueEmpty;

if (isset($_bootStrap) && !trueEmpty($_bootStrap)) {
    echo $_bootStrap;
}
?>
<?php
if (isset($_title)) {
    ?>
    <div class="_title"><?php echo StrFilter::HTMLencode($_title); ?></div>
    <?php
}
?>
<?php
$groups = $_viewer->getTableView()->getEffectiveGroups();
if ($groups) {
    foreach ($groups as $group) {
        if (!\zfx\trueEmpty($group->title)) {
            if ($group->id != '') {
                $attrId = "id=\"{$group->id}\"";
            }
            else {
                $attrId = '';
            }
            echo '<div ' . $attrId . ' class="_fieldGroupTitle">' . \zfx\StrFilter::HTMLencode($group->title) . '</div>';
        }
        $_keys       = $group->getFields();
        $_groupClass = ($group->expanded ? '_fieldGroupExpanded' : '');
        $_style      = '';
        if ($group->minHeight != '') {
            $_style = "style=\"min-height: {$group->minHeight};\"";
        }
        if ($_keys) {
            echo "<div class=\"_fieldGroup {$_groupClass}\" {$_style}>";
            $cols = $group->getCols();
            $t    = 1;
            $i    = 1;
            if ($cols) {
                echo '<table class="struct _fieldGroupTable">';
            }
            $numKeys = count($_keys);
            foreach ($_keys as $_key) {
                $_field = $_viewer->getTableView()->getFieldView($_key);
                if (is_a($_field, '\zfx\FieldViewFormElement')) {
                    $dataZafFieldName = "data-zaf-field-name=\"{$_field->getElementName()}\"";
                }
                else {
                    $dataZafFieldName = '';
                }
                if (!$_field) {
                    continue;
                }
                if (is_a($_field, '\zfx\FieldViewHidden')) {
                    $_field->render(a($_data, $_key));
                }
                else {
                    if ($cols) {
                        if ($i == 1) {
                            echo '<tr>';
                        }
                        $colSpan = '';
                        if ($t == $numKeys && $i < $cols) {
                            $colSpan = 'colspan="' . (($cols - $i) * 2 + 1) . '"';
                        }
                        ?>
                        <td <?php echo $dataZafFieldName; ?>>
                            <div class="_label"><?php echo $_field->getLabel(); ?></div>
                        </td>
                        <td class="_field" <?php echo $colSpan; ?> <?php echo $dataZafFieldName; ?>>
                            <div class="_field"><?php $_field->render(a($_data, $_key)); ?></div>
                        </td>
                        <?php
                        $i++;
                        $t++;
                        if ($i > $cols) {
                            echo '</tr>';
                            $i = 1;
                        }
                    }
                    else {
                        ?>
                    <div class="_element" <?php echo $dataZafFieldName; ?>>
                        <div class="_label"><?php echo $_field->getLabel(); ?></div>
                        <div class="_field"><?php $_field->render(a($_data, $_key)); ?></div>
                        </div><?php
                    }
                }
            }
            if ($cols) {
                echo "</table>";
            }
            echo "</div>";
        }
        if (!\zfx\trueEmpty($group->info)) {
            echo '<div class="_fieldGroupInfo">' . \zfx\StrFilter::HTMLencode($group->info) . '</div>';
        }
    }
}
if (\zfx\va($_interdeps)) {
    ?>
    <script type="text/javascript">
        zfx.Zaf.formInterDeps('<?php echo $_submitFormID; ?>', JSON.parse('<?php echo json_encode($_interdeps); ?>'));
    </script><?php }
