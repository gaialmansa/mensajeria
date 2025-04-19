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
    <table class="_rowView struct">
        <?php
        if (isset($_title)) {

            ?>
            <tr>
                <th class="_title"
                    colspan="2>"><?php echo StrFilter::HTMLencode($_title); ?></th>
            </tr>
            <?php
        }

        ?>
        <?php
        foreach ($_viewer->getTableView()->getFieldViews() as $_key => $_field) {
            if (is_a($_field, '\zfx\FieldViewFormElement')) {
                $dataZafFieldName = "data-zaf-field-name=\"{$_field->getElementName()}\"";
            }
            else {
                $dataZafFieldName = '';
            }
            if (is_a($_field, '\zfx\FieldViewHidden')) {
                $_field->render(a($_data, $_key));
            }
            else {

                ?>
                <tr>
                    <th <?php echo $dataZafFieldName; ?>>
                        <?php echo $_field->getLabel(); ?>
                    </th>
                    <td class="<?php echo $_field->getOwnCssClass(); ?>" <?php echo $dataZafFieldName; ?>>
                        <?php $_field->render(a($_data, $_key)); ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?></table><?php if (\zfx\va($_interdeps)) {
    ?>
    <script type="text/javascript">
        zfx.Zaf.formInterDeps('', JSON.parse('<?php echo json_encode($_interdeps); ?>'));
    </script><?php }
