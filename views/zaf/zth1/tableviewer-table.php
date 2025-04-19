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

// Compute table width
use zfx\SelectorCheckBox;
use zfx\StrFilter;
use zfx\TableActions;
use zfx\TableViewer;
use function zfx\a;
use function zfx\trueEmpty;
use function zfx\va;

$_numCols = $_viewer->getTableView()->getFieldCount();
if ($_viewer->getSelectorType() != TableViewer::SELECTOR_TYPE_NONE) {
    $_numCols++;
}
if (isset($_title)) {
    $_title = StrFilter::HTMLencode($_title);
    echo "<div class='_title'>$_title</div>";
}

if (isset($_paginator) || isset($_actions)) {
    /*
     * TOOLBAR
     */

    ?>
    <div class="_toolBar"><?php
    /*
     * TOOLBAR > PAGINATOR
     */
    if (isset($_paginator)) { ?>
        <div class="_paginator"><?php echo $_paginator; ?></div><?php
    }
    else {
        if ($_rowCount > 0) { ?>
            <div class="_paginator">
            Total: <?php echo $_rowCount; ?>
            </div><?php
        }
    }
    /*
     * TOOLBAR > ACTIONS
     */
    if (isset($_actions) && va($_actions)) {
        foreach ($_actions as $_act) {
            TableActions::renderAction($_act, $_lang, (bool)$_data);
        }
    }

    ?>
    <div style="clear: both;"></div>
    </div><?php
}

// Fast inserter
if (isset($_fastInserter) && $_fastInserter) {
    $_fastInserter->render();
}

// Table
?>
    <div class="_tableViewContainer <?php if (isset($_tvClass)) {
        echo $_tvClass;
    } ?>">
        <table class="_tableView">
            <thead><?php
            /*
             * TABLE > HEADERS
             */
            if ((isset($_headers) && $_headers === TRUE && $_data) || $_viewer->getSelectorType() != TableViewer::SELECTOR_TYPE_NONE) {

                ?>
                <tr class="_header"><?php
                if (isset($_rowActions)) {

                    ?>
                    <th></th><?php
                }
                if ($_viewer->getSelectorType() != TableViewer::SELECTOR_TYPE_NONE) {
                    if ($_viewer->getAllSelected()) {
                        $_checked = 'checked="checked"';
                    }
                    else {
                        $_checked = '';
                    }

                    ?>
                    <th><input type="checkbox" value="checked"
                               class="zjCheckAll" <?php echo $_checked; ?>/>
                    </th><?php
                }
                if ($_viewer->getTableView()->getFieldViews()) {
                    foreach ($_viewer->getTableView()->getFieldViews() as $_key => $_field) {
                        if ($_field->isHidden()) continue;
                        ?>
                    <th class="zjDraggable zjDroppable zjFieldHeader"
                        data-adm-field="<?php echo StrFilter::safeEncode($_key); ?>">
                        <div class="_colContainer">
                            <div class="_colLabel"><?php echo $_field->getLabel(); ?></div>
                            <div class="_colButton"><?php if ($_viewer->getSortButton()) {
                                    $_viewer->getSortButton()->renderButton($_key);
                                } ?></div>
                        </div></th><?php
                    }
                }

                ?></tr><?php } ?></thead>
            <tbody><?php
            /*
             * TABLE > ROWS
             */
            if ($_data) {
                $_r = 1;
                foreach ($_data as $_d) {
                    $rowClass = '';
                    if (isset($_customRowClasser)) {
                        $rowClass = call_user_func($_customRowClasser, $_d, $_r);
                    }
                    else {
                        $rowClass = ($_r % 2 === 0 ? '_even' : '_odd');
                    }
                    ?>
                    <tr class="<?php if (isset($zjClickOnRow) && $zjClickOnRow) echo 'zjClickOnRow'; ?> zjData <?php echo($rowClass); ?>"><?php
                    // row actions column
                    if (isset($_rowActions)) {

                        ?>
                        <td class="_rowActions"><?php $_rowActions->render($_d); ?></td><?php
                    }
                    // Selector column
                    if ($_viewer->getSelectorType() != TableViewer::SELECTOR_TYPE_NONE) {

                        ?>
                        <td class="_selector"><?php SelectorCheckBox::renderTag($_viewer->getAllSelected() || $_viewer->rowIsSelected($_d),
                            $_viewer->getKeyValue($_d)); ?></td><?php
                    }
                    // Fields
                    foreach ($_viewer->getTableView()->getFieldViews() as $_key => $_field) {
                        if (is_a($_field, '\zfx\FieldViewHidden')) {
                            $_field->render(a($_d, $_key), $_viewer->getKeyValue($_d));
                        }
                        else {
                            $colClass = '';
                            if (is_callable($_field->getCustomColClasser())) {
                                $colClass = call_user_func($_field->getCustomColClasser(), a($_d, $_key), $_d, $_r);
                            }
                            else {
                                $colClass = $_field->getOwnCssClass();
                            }
                            ?>
                            <td class="zjData <?php echo $colClass; ?>"><?php
                            if (is_a($_field, '\zfx\FieldViewText')) { ?>
                                <?php $_field->render(a($_d, $_key), $_viewer->getKeyValue($_d)); ?><?php
                            }
                            else {
                                // Si tenemos campos de tipo variable, lo gestionamos aquí
                                if ($_varTypeField && $_key == $_varTypeField) {
                                    $changedField = $_field;
                                    \zfx\DataVarType::varTypeChange($_viewer->getTableView(), $_varTypeField, $_dbFileUrl, $_loc, a($_d, $_varTypeOrigin), a($_d, $_varTypeOptions));
                                    $_viewer->getTableView()->getFieldView($_key)->render(a($_d, $_key), $_viewer->getKeyValue($_d));
                                    $_viewer->getTableView()->setFieldView($_key, $changedField);
                                }
                                else {
                                    // Si no, mostrar campo normalmente.
                                    $_field->render(a($_d, $_key), $_viewer->getKeyValue($_d));
                                }
                            } ?></td><?php
                        }
                    } ?></tr><?php
                    $_r++;
                }
            }

            ?></tbody>
        </table>
    </div>
<?php
if (isset($_bootStrap) && !trueEmpty($_bootStrap)) {
    echo $_bootStrap;
}
