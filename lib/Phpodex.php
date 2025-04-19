<?php
/**
 * PHP OpenDocument Expander
 * Copyright 2012-2023 by Jorge A. Montes Perez <jorge@zerfrex.com>
 * All rights reserved.
 */
if (PHP_MAJOR_VERSION < 8) {
    libxml_disable_entity_loader(FALSE);
}

class PhpodexBlock
{

    public $singleNode;
    public $multiNode;
    public $doc;

    public $logFile = '';

    // --------------------------------------------------------------------

    public function __construct($node, $logFile = '')
    {
        if (is_array($node)) {
            $this->singleNode = NULL;
            $this->multiNode  = $node;
            $el               = current($node);
            $this->doc        = $el->ownerDocument;
        }
        else /* $node instanceof DOMNode */ {
            $this->singleNode = $node;
            $this->multiNode  = NULL;
            $this->doc        = $node->ownerDocument;
        }
        $this->logFile = $logFile;
    }

    // --------------------------------------------------------------------

    protected function nwcount($var)
    {
        if (is_array($var) || $var instanceof Countable) {
            return count($var);
        }
        else {
            return 0;
        }
    }

    // --------------------------------------------------------------------


    // --------------------------------------------------------------------

    public function exterminate()
    {
        if ($this->singleNode) {
            $this->singleNode->parentNode->removeChild($this->singleNode);
        }
        else {
            foreach ($this->multiNode as $node) {
                $node->parentNode->removeChild($node);
            }
        }
    }

    // --------------------------------------------------------------------

    public function insert($block)
    {
        if ($this->singleNode) {
            $target = $this->singleNode;
        }
        else {
            $target = $this->multiNode[0];
        }
        if ($block->singleNode) {
            $target->parentNode->insertBefore($block->singleNode, $target);
        }
        else {
            foreach ($block->multiNode as $node) {
                $target->parentNode->insertBefore($node, $target);
            }
        }
    }

    // --------------------------------------------------------------------

    public function copy()
    {
        $this->log('Llamada a copy().');
        if ($this->singleNode) {
            $this->log('Será un singleNode');
            return new PhpodexBlock($this->singleNode->cloneNode(TRUE), $this->logFile);
        }
        else {
            $this->log('Será un multiNode');
            $newMultiNode = array();
            $max          = $this->nwcount($this->multiNode);
            $this->log("Total bloques a copiar: $max");
            // Evitamos el primero y el último porque son el inicio y fin del bloque.
            for ($i = 1; $i < $max - 1; $i++) {
                $this->log("Copiando bloque $i");
                $newMultiNode[] = $b = $this->multiNode[$i]->cloneNode(TRUE);
                $this->log("Resultado copia bloque $i", $b);

            }
            return new PhpodexBlock($newMultiNode, $this->logFile);
        }
    }

    // --------------------------------------------------------------------

    public function getFields($fieldList)
    {
        $fields = array();
        if ($this->singleNode) {
            $nodesToBeChecked = array($this->singleNode);
        }
        else {
            $nodesToBeChecked = &$this->multiNode;
        }
        foreach ($nodesToBeChecked as $node) {
            // Text User variables
            foreach ($node->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:text:1.0', 'user-field-get') as $element) {
                if ($element->prefix == 'text') {
                    $name = $element->getAttribute('text:name');
                    if (in_array($name, $fieldList)) {
                        $fields[] = $element;
                    }
                }
            }
            // Images
            foreach ($node->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:drawing:1.0', 'frame') as $element) {
                if ($element->prefix == 'draw') {
                    $name = $element->getAttribute('draw:name');
                    if (in_array($name, $fieldList)) {
                        $fields[] = $element;
                    }
                }
            }
        }
        return $fields;
    }

    // --------------------------------------------------------------------

    public function log($title, $data = '__!__NO_DATA__!__')
    {
        if ($this->logFile == '') return;
        if (!is_string($data)) {
            $data = print_r($data, TRUE);
        }
        elseif (is_null($data)) {
            $data = "(NULL)";
        }
        elseif (is_bool($data)) {
            $data = ($data ? '(TRUE)' : '(FALSE)');
        }
        elseif ($data === '') {
            $data = "(EMPTY STRING)";
        }
        else {
            $data = (string)$data;
        }
        $def = @fopen($this->logFile, 'a');
        if ($def !== FALSE) {
            $now = gettimeofday();
            fprintf($def, "[Phpodex][%s.%06u][%s]\n", date('Y-m-d H:i:s', $now['sec']), $now['usec'], $title);
            if ($data !== '__!__NO_DATA__!__') {
                fprintf($def, "%s\n", $data);
            }
            fclose($def);
        }
    }
    // --------------------------------------------------------------------


}


// --------------------------------------------------------------------
// --------------------------------------------------------------------
// --------------------------------------------------------------------


class Phpodex
{

    const ERROR_NO_ERROR        = 0;
    const ERROR_FILE_NOT_EXISTS = 1;
    const ERROR_CANNOT_CLOSE    = 2;
    const ERROR_CANNOT_OPEN     = 3;
    const ERROR_NO_FILE_OPENED  = 4;
    const ERROR_OPENING_XML     = 5;
    const MODE_BODY             = 0;
    const MODE_HEADER_FOOTER    = 1;
    public    $errorCode;
    protected $workPath;
    protected $tempdir;
    protected $fileName;
    protected $xmlFile;
    protected $mode;
    protected $newImgList;
    public    $logFile = '';

    /**
     * @var boolean Si es TRUE los campos de tipo imagen proporcionados con un nombre de fichero (valor) en blanco se borrarán
     */
    protected $deleteImageIfBlank;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $file Complete path and file name of the ODT template file to work on
     */
    public function __construct($file = NULL)
    {
        $this->tempdir    = '/tmp';
        $this->newImgList = array();
        $this->errorCode  = self::ERROR_NO_ERROR;
        $this->workPath   = NULL;
        $this->fileName   = NULL;
        $this->setMode(self::MODE_BODY);
        if ($file) {
            $this->open($file);
        }
        $this->deleteImageIfBlank = TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * @return string
     */
    public function getTempdir()
    {
        return $this->tempdir;
    }

    // --------------------------------------------------------------------

    /**
     * @param string $tempdir
     */
    public function setTempdir($tempdir)
    {
        $this->tempdir = (string)$tempdir;
    }

    // --------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isDeleteImageIfBlank()
    {
        return $this->deleteImageIfBlank;
    }

    // --------------------------------------------------------------------

    /**
     * @param bool $deleteImageIfBlank
     */
    public function setDeleteImageIfBlank($deleteImageIfBlank)
    {
        $this->deleteImageIfBlank = (bool)$deleteImageIfBlank;
    }


    // --------------------------------------------------------------------

    /**
     * Close previously opened file and open a new one
     *
     * @param string $file Complete path and file name of the ODT template file to work on
     */
    public function open($file)
    {
        if (!file_exists($file)) {
            $this->errorCode = self::ERROR_FILE_NOT_EXISTS;
            return FALSE;
        }

        // Is there an opened file? Close it
        if ($this->fileName) {
            if (!$this->close($this->fileName)) {
                $this->errorCode = self::ERROR_CANNOT_CLOSE;
                return FALSE;
            }
        }

        // Unzip file
        $this->fileName = $file;
        if (!$this->decompress()) {
            // Fail
            $this->fileName  = NULL;
            $this->errorCode = self::ERROR_CANNOT_OPEN;
            return FALSE;
        }
        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Close and save file
     *
     * @param string $file Complete path and file name of the output file
     * @return boolean FALSE on error
     */
    public function close($file = NULL)
    {
        if (!$this->fileName) {
            $this->errorCode = self::ERROR_NO_FILE_OPENED;
            return FALSE;
        }

        // Si hay nuevos ficheros de imagen, modificar el manifest
        if ($this->newImgList) {
            $this->modifyManifest();
        }

        // Si no se especificó, usar el mismo nombre con el que se abrió.
        if (!$file) {
            $file = $this->fileName;
        }

        // Si logramos comprimirlo, cerramos todo.
        if ($this->compress($file)) {
            self::delTree($this->workPath);
            $this->fileName = NULL;
            $this->workPath = NULL;
            return TRUE;
        }
        else {
            $this->errorCode = self::ERROR_CANNOT_CLOSE;
            return FALSE;
        }
    }

    // --------------------------------------------------------------------

    private function modifyManifest()
    {
        $doc = new DOMDocument();
        @$doc->load($this->workPath . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR . 'manifest.xml');
        $m = $doc->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:manifest:1.0', 'manifest')->item(0);
        foreach ($this->newImgList as $fileName) {
            $newName = strtoupper(md5($fileName)) . '.' . strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            copy($fileName, $this->workPath . DIRECTORY_SEPARATOR . 'Pictures' . DIRECTORY_SEPARATOR . $newName);
            $element = $doc->createElement('manifest:file-entry');
            $element->setAttribute('manifest:full-path', "Pictures/$newName");
            $mimeImage = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if ($mimeImage == 'jpg') $mimeImage = 'jpeg';
            $element->setAttribute('manifest:media-type', 'image/' . $mimeImage);
            $m->appendChild($element);
        }
        $doc->save($this->workPath . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR . 'manifest.xml');
    }

    // --------------------------------------------------------------------

    private function compress($target)
    {

        $target = realpath(dirname($target)) . DIRECTORY_SEPARATOR . basename($target);
        $res    = TRUE;
        if (file_exists($target) && is_writable($target)) {
            $res = unlink($target);
        }
        if (!$res) {
            return FALSE;
        }
        $here = getcwd();
        chdir($this->workPath);
        exec("zip -0 -X '$target' 'mimetype'");
        exec("zip -r '$target' * -x mimetype");
        $exists = file_exists($target);
        chdir($here);
        return $exists;
    }

    // --------------------------------------------------------------------

    public static function delTree($directory)
    {
        if (is_dir($directory)) {
            $contents = scandir($directory);
            foreach ($contents as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($directory . '/' . $object) == 'dir') {
                        self::delTree($directory . '/' . $object);
                    }
                    else {
                        unlink($directory . '/' . $object);
                    }
                }
            }
            reset($contents);
            rmdir($directory);
        }
    }

    // --------------------------------------------------------------------

    private function decompress()
    {
        // Creo un directorio temporal en $ruta
        $tdpath = tempnam($this->tempdir, 'des');
        if ($tdpath === FALSE) {
            return FALSE;
        }
        unlink($tdpath);
        if (mkdir($tdpath)) {
            // Almacenamos el directorio donde se ha comprimido.
            $this->workPath = $tdpath;
            // Descomprimir
            exec("unzip '{$this->fileName}' -d '$tdpath'");
            return TRUE;
        }
        return FALSE;
    }

    // --------------------------------------------------------------------

    public function insertFields($fieldList)
    {
        if (!$this->fileName) {
            $this->errorCode = self::ERROR_NO_FILE_OPENED;
            return FALSE;
        }

        // @TODO return FALSE cuando haya problemas
        $doc = new DOMDocument();
        libxml_disable_entity_loader(FALSE);
        $res = @$doc->load($this->workPath . DIRECTORY_SEPARATOR . "content.xml");
        if ($res === FALSE) {
            $this->errorCode = self::ERROR_OPENING_XML;
            return FALSE;
        }

        // Buscaré el primer text:h o text:p, que es lo primero que aparece ANTES de los campos.
        foreach ($doc->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:text:1.0', '*') as $element) {
            if (in_array($element->localName, array('p', 'h')) && $element->prefix == 'text') {
                // Insertamos el contenedor
                $node    = $doc->createElement('text:user-field-decls');
                $newNode = $element->parentNode->insertBefore($node, $element);

                // Insertamos los campos
                foreach ($fieldList as $k => $v) {
                    $other = $doc->createElement('text:user-field-decl');
                    $other->setAttribute('office:value-type', 'string');
                    $other->setAttribute('office:string-value', $v);
                    $other->setAttribute('text:name', $k);
                    $newNode->appendChild($other);
                }
                break;
            }
        }
        $doc->save($this->workPath . DIRECTORY_SEPARATOR . "content.xml");
    }

    // --------------------------------------------------------------------

    public function cleanUp()
    {
        if (!$this->fileName) {
            $this->errorCode = self::ERROR_NO_FILE_OPENED;
            return FALSE;
        }


        // El documento
        $doc = new DOMDocument();
        $res = @$doc->load($this->workPath . DIRECTORY_SEPARATOR . $this->xmlFile);
        if ($res === FALSE) {
            $this->errorCode = self::ERROR_OPENING_XML;
            return FALSE;
        }

        // Para la limpieza usaremos XPath.
        $x = new DOMXPath($doc);

        // Aquellas variables que no hayan sido sustituidas, las dejamos en blanco.
        $elements = $x->query('//text:user-field-get');
        foreach ($elements as $element) {
            if ($element->prefix == 'text') {
                $this->replaceTextNode($element, '', $doc);
            }
        }

        // Voy a buscar todas las tablas que tenga vacías
        $elements = $x->query('//table:table[not(*//text:p[text()] or *//text:p[node()])]');
        foreach ($elements as $e) {
            $e->parentNode->removeChild($e);
        }

        // También los párrafos vacíos.
        $elements = $x->query('//text:p[count(text:span[not(text())])=count(node())]|//text:h[count(text:span[not(text())])=count(node())]');
        foreach ($elements as $e) {
            $e->parentNode->removeChild($e);
        }

        // Y a grabar!
        $doc->save($this->workPath . DIRECTORY_SEPARATOR . $this->xmlFile);
    }

    // --------------------------------------------------------------------

    public function fillRecord($data)
    {
        if (!$this->fileName) {
            $this->errorCode = self::ERROR_NO_FILE_OPENED;
            return FALSE;
        }


        $doc = new DOMDocument();
        $res = @$doc->load($this->workPath . DIRECTORY_SEPARATOR . $this->xmlFile);
        if ($res === FALSE) {
            $this->errorCode = self::ERROR_OPENING_XML;
            return FALSE;
        }

        // Voy a obtener los campos afectados
        $nodes = array();
        // Campos de texto
        foreach ($doc->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:text:1.0', 'user-field-get') as $element) {
            if ($element->prefix == 'text') {
                $nombre = $element->getAttribute('text:name');
                if (is_array($data)) if (array_key_exists($nombre, $data)) {
                    $nodes[] = $element;
                }
            }
        }
        // Imágenes
        foreach ($doc->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:drawing:1.0', 'frame') as $element) {
            if ($element->prefix == 'draw') {
                $nombre = $element->getAttribute('draw:name');
                if (is_array($data)) if (array_key_exists($nombre, $data)) {
                    $nodes[] = $element;
                }
            }
        }
        // Los sustituimos
        if ($nodes) {
            foreach ($nodes as &$element) {
                if ($element->tagName == 'text:user-field-get') {
                    $this->replaceTextNode($element, $data[$element->getAttribute('text:name')], $doc);
                }
                elseif ($element->tagName == 'draw:frame') {
                    $this->replaceImgNode($element, $data[$element->getAttribute('draw:name')]);
                }
            }
        }
        $doc->save($this->workPath . DIRECTORY_SEPARATOR . $this->xmlFile);
    }

    // --------------------------------------------------------------------

    private function replaceTextNode(&$element, $text, &$doc)
    {
        $chunks = preg_split('%(?:\n|\r)+%isu', $this->sanitize($text));
        $cnt    = $this->nwcount($chunks);
        for ($i = 0; $i < $cnt; $i++) {
            $element->parentNode->insertBefore($doc->createTextNode($chunks[$i]), $element);
            if ($i < $cnt - 1 && $cnt > 1) {
                $element->parentNode->insertBefore($doc->createElement('text:line-break'), $element);
            }
        }
        $element->parentNode->removeChild($element);
    }

    // --------------------------------------------------------------------

    protected function nwcount($var)
    {
        if (is_array($var) || $var instanceof Countable) {
            return count($var);
        }
        else {
            return 0;
        }
    }

    // --------------------------------------------------------------------

    public function sanitize($txt)
    {
        $txt = preg_replace(array('%[\x00-\x09]%', '%[\x0a\x0d]+%', '%[\x0e-\x1f]%', '%[\x0b-\x0c]%'), array('', "\n", '', ''), (string)$txt);
        return $txt;
    }

    // --------------------------------------------------------------------

    public function replaceImgNode(&$element, $imgFileName)
    {
        $newFrame = $element->cloneNode(TRUE);
        $newImage = $newFrame->firstChild;
        if ($newImage->tagName == 'draw:image' && is_file($imgFileName) && is_readable($imgFileName)) {
            $newName = strtoupper(md5($imgFileName)) . '.' . strtolower(pathinfo($imgFileName, PATHINFO_EXTENSION));
            $newImage->setAttribute('xlink:href', 'Pictures/' . $newName);
            $element->parentNode->insertBefore($newFrame, $element);
            $element->parentNode->removeChild($element);
            $this->newImgList[] = $imgFileName;
        }
        else {
            if ($imgFileName === '' && $this->deleteImageIfBlank) {
                $element->parentNode->removeChild($element);
            }
        }
    }

    // --------------------------------------------------------------------

    public function getFileName()
    {
        return $this->fileName;
    }

    // --------------------------------------------------------------------

    public function getMode()
    {
        return $this->mode;
    }

    // --------------------------------------------------------------------

    public function setMode($mode = 0)
    {
        $this->mode = (int)$mode;
        if ($this->mode > self::MODE_HEADER_FOOTER || $this->mode < self::MODE_BODY) {
            $mode = self::MODE_BODY;
        }
        $files         = array(
            self::MODE_BODY          => 'content.xml',
            self::MODE_HEADER_FOOTER => 'styles.xml'
        );
        $this->xmlFile = $files[$this->mode];
    }

    // --------------------------------------------------------------------

    private function expandBlock(PhpodexBlock $block, &$data, $availColumns, DOMDocument $doc = NULL)
    {
        $this->log('Llamada a expandBlock(). Bloque', $block);
        $this->log('Datos a usar para la expansión del bloque', $data);
        $record = reset($data);
        while ($record) {
            // Creamos una copia de la fila o bloque
            $newBlock = $block->copy();
            $this->log('Bloque copiado (debería ser idéntico)', $newBlock);

            // Sustituimos en dicha copia por los datos:
            // Voy a obtener todos los campos afectados
            $nodes = $newBlock->getFields($availColumns);
            $this->log('Nodos encontrados en el bloque', $nodes);

            // Los sustituimos
            if ($nodes) {
                foreach ($nodes as &$element) {
                    if ($element->tagName == 'text:user-field-get') {
                        if (array_key_exists($element->getAttribute('text:name'), $record)) {
                            $this->replaceTextNode($element, $record[$element->getAttribute('text:name')], $newBlock->doc);
                        }
                        else {
                            $this->replaceTextNode($element, '', $newBlock->doc);
                        }
                    }
                    elseif ($element->tagName == 'draw:frame') {
                        if (array_key_exists($element->getAttribute('draw:name'), $record)) {
                            $this->replaceImgNode($element, $record[$element->getAttribute('draw:name')]);
                        }
                        else {
                            $this->replaceImgNode($element, '');
                        }
                    }
                }
            }

            // Añadimos dicha copia modificada
            $block->insert($newBlock);


            // Si se especificó un documento, es que estamos rellenando recursivamente
            if ($doc) {
                // Veamos si hay tablas hijas
                foreach ($record as $k => $v) {
                    if (is_array($v)) {
                        $this->fillBlock($k, $v, $doc, $newBlock);
                    }
                }
            }

            // Siguiente registro
            $record = next($data);
        }
        // Elimino la original
        $block->exterminate();
    }

    // --------------------------------------------------------------------

    public function fillBlock($blockName, array $data, DOMDocument $doc = NULL, PhpodexBlock $context = NULL)
    {
        if (!$this->fileName) {
            $this->errorCode = self::ERROR_NO_FILE_OPENED;
            return FALSE;
        }

        $this->log("Llamada a fillBlock(), nombre de bloque: $blockName");
        $this->log("Datos", $data);


        // El documento
        $wholeDoc = FALSE;
        if (!$doc) {
            $wholeDoc = TRUE;
            $doc      = new DOMDocument();
            $res      = @$doc->load($this->workPath . DIRECTORY_SEPARATOR . $this->xmlFile);
            if ($res === FALSE) {
                $this->errorCode = self::ERROR_OPENING_XML;
                return FALSE;
            }
            $context = new PhpodexBlock($doc->childNodes->item(0), $this->logFile);
        }

        // En dicho bloque voy a buscar los bloques susceptibles de ser expandidos
        // Las columnas disponibles son todas menos las que sean un array.
        // Si data está vacío, el bloque resulta eliminado.
        // Cada uno de los registros (elementos de $data) puede tener diferentes variables. Voy a escanear todo $data para obtener las diferentes
        // variables (claves) que nos han pasado. Lo guardaremos en $reg.
        $reg        = [];
        $predicates = [];
        if (count($data)) {
            foreach ($data as $d) {
                if (is_array($d)) {
                    foreach ($d as $k => $v) {
                        // Solo guardaremos las variables que a su vez no sean arrays, o sea, subbloques.
                        if (!is_array($v)) {
                            $reg[] = $k;
                        }
                    }
                }
            }
            if ($reg) {
                foreach ($reg as $name) {
                    $predicates[$name] = "@text:name=\"$name\" or @draw:name=\"$name\"";
                }
            }
        }
        if (!$reg) return;
        $this->log('Predicados', $predicates);
        $predicate = implode(' or ', $predicates);
        $this->log('Ahora se buscará bloques compatibles');
        do {
            $blocks = $this->searchBlocks($doc, $context, $blockName, $predicate);
            $this->log('Bloques encontrados', $blocks);
            if ($blocks) {
                $this->log('Expandiendo cada bloque con datos');
                foreach ($blocks as &$b) {
                    // Ahora para cada bloque, voy a añadir las que se necesiten
                    $this->expandBlock($b, $data, array_keys($predicates), $doc);
                }
            }
            if (!$wholeDoc) {
                break;
            } // Solo buscaremos otros bloques cuando estamos buscando en todo el documento
        } while ($blocks);


        // Y a grabar!
        $doc->save($this->workPath . DIRECTORY_SEPARATOR . $this->xmlFile);
    }

    // --------------------------------------------------------------------

    public function searchBlocks($doc, PhpodexBlock $where, $name, $predicate)
    {
        if ($where->singleNode) {
            $nodeList = array($where->singleNode);
        }
        else {
            $nodeList = $where->multiNode;
        }

        $blocks = array();
        $x      = new DOMXPath($doc);
        $start  = NULL;
        $end    = NULL;
        foreach ($nodeList as $node) {
            // Buscaremos párrafos marcados.
            if (!$start) {
                $startElements = $x->query("descendant-or-self::text:p[.//text:user-field-get[starts-with(@text:name,'ckb')][.='$name']]", $node);
                if ($startElements && $startElements->length > 0) {
                    $start = $startElements->item(0);
                }
            }
            if ($start) {
                $endElements = $x->query("descendant-or-self::text:p[.//text:user-field-get[starts-with(@text:name,'ckk')][.='$name']]", $node);
                if ($endElements && $endElements->length > 0) {
                    $end = $endElements->item(0);
                }
            }
            $this->log('Construcción del PhpodexBlock');
            if ($start && $end) {
                $parr   = array();
                $this->log('Start', $start);
                $parr[] = $start;
                $ns     = $start->nextSibling;
                while ($ns) {
                    if ($ns->isSameNode($end)) {
                        $this->log('Siguiente era el final (end)');
                        break;
                    }
                    $this->log('Siguiente', $ns);
                    $parr[] = $ns;
                    $ns     = $ns->nextSibling;
                }
                $this->log('End', $end);
                $parr[]   = $end;
                $blocks[] = new PhpodexBlock($parr, $this->logFile);
                $start    = NULL;
                $end      = NULL;
            }
        }
        //
        // Si no había párrafos marcados, buscaremos una tabla
        if (!$blocks) {
            foreach ($nodeList as $node) {
                if (!$predicate) {
                    continue;
                }
                // Buscar filas de tabla que contengan campos
                $query    = 'descendant-or-self::table:table[@table:name="' . $name . '" or starts-with(@table:name,"' . $name . '__")]//table:table-row[table:table-cell//text:user-field-get[' . $predicate . '] or table:table-cell//draw:frame[' . $predicate . ']]';
                $elements = $x->query($query, $node);
                if ($elements) {
                    foreach ($elements as $e) {
                        $blocks[] = new PhpodexBlock($e, $this->logFile);
                    }
                }
            }
        }
        return $blocks;
    }

    // --------------------------------------------------------------------

    public function log($title, $data = '__!__NO_DATA__!__')
    {
        if ($this->logFile == '') return;
        if (!is_string($data)) {
            $data = print_r($data, TRUE);
        }
        elseif (is_null($data)) {
            $data = "(NULL)";
        }
        elseif (is_bool($data)) {
            $data = ($data ? '(TRUE)' : '(FALSE)');
        }
        elseif ($data === '') {
            $data = "(EMPTY STRING)";
        }
        else {
            $data = (string)$data;
        }
        $def = @fopen($this->logFile, 'a');
        if ($def !== FALSE) {
            $now = gettimeofday();
            fprintf($def, "[Phpodex][%s.%06u][%s]\n", date('Y-m-d H:i:s', $now['sec']), $now['usec'], $title);
            if ($data !== '__!__NO_DATA__!__') {
                fprintf($def, "%s\n", $data);
            }
            fclose($def);
        }
    }

    // --------------------------------------------------------------------

}
