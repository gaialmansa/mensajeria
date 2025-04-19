/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */


// ------------------------------------------------------------------------

var zfx;
if (!zfx) zfx = {};
zfx.StrTools = {};


// ------------------------------------------------------------------------


zfx.StrTools.repeat = function (inputString, times) {
    return (new Array(times + 1)).join(inputString);
}


// ------------------------------------------------------------------------


zfx.StrTools.fixedFromCharCode = function (utf8code) {
    if (utf8code > 0xFFFF) {
        utf8code -= 0x10000;
        return String.fromCharCode(0xD800 + (utf8code >> 10), 0xDC00 + (utf8code & 0x3FF));
    } else {
        return String.fromCharCode(utf8code);
    }
}


// ------------------------------------------------------------------------


zfx.StrTools.safeDecode = function (inputString) {
    expanded = inputString.replace(/([g-n])/g, function (s) {
        return zfx.StrTools.repeat('0', s.charCodeAt(0) - 101);
    });
    let ret = new String();
    for (i = 0; i < expanded.length; i = i + 8) {
        ret = ret + zfx.StrTools.fixedFromCharCode(
            parseInt(expanded.substring(i, i + 2), 16) * 16777216 +
            parseInt(expanded.substring(i + 2, i + 4), 16) * 65536 +
            parseInt(expanded.substring(i + 4, i + 6), 16) * 256 +
            parseInt(expanded.substring(i + 6, i + 8), 16)
        );
    }
    return ret;
}

// ------------------------------------------------------------------------

zfx.StrTools.safeEncode = function (inputString) {
    bytes = zfx.StrTools.convertToByte4be(inputString);
    let ret = new String();
    for (i = 0; i < bytes.length; i++) {
        ret = ret + bytes[i].toString(16).padStart(2, '0');
    }
    let regex = /(0{2,9})/g;
    let replacedString = ret.replace(regex, function(match, group) {
        return String.fromCharCode(101 + group.length);
    });
    return replacedString;
}


// ------------------------------------------------------------------------

zfx.StrTools.convertToByte4be = function(inputString) {
    var bytes = [];
    for (var i = 0; i < inputString.length; i++) {
        var codePoint = inputString.codePointAt(i);
        var byte1 = (codePoint >> 24) & 0xFF;
        var byte2 = (codePoint >> 16) & 0xFF;
        var byte3 = (codePoint >> 8) & 0xFF;
        var byte4 = codePoint & 0xFF;
        bytes.push(byte1);
        bytes.push(byte2);
        bytes.push(byte3);
        bytes.push(byte4);
        if (codePoint > 0xFFFF) {
            i++; // Increment i by 1 additional time for code points outside the BMP
        }
    }
    return new Uint8Array(bytes);
}

// ------------------------------------------------------------------------


/*
 *  Extrae datos desde source usando una regex compuesta con prefix+format.
 *  format debería contener unos paréntesis para obtener solo lo que se
 *  necesite.
 *  Solo extrae el primero que encuentra.
 */
zfx.StrTools.extractData = function (source, prefix, format) {
    reg_ex = new RegExp(prefix + format);
    result = source.match(reg_ex);
    if (result) {
        result.shift();
        if (result.length == 1) {
            return result[0];
        } else {
            return result;
        }
    } else {
        return null;
    }
}

/*
 *  Extrae datos desde source usando una regex compuesta con prefix+format.
 *  format debería contener unos paréntesis para obtener solo lo que se
 *  necesite.
 *  Extrae todos los que encuentra
 */
zfx.StrTools.extractAllData = function (source, prefix, format) {
    reg_ex = new RegExp(prefix + format, 'g');
    ret = new Array();
    regres = reg_ex.exec(source);
    if (regres) {
        while (regres) {
            regres.shift();
            ret.push(regres);
            regres = reg_ex.exec(source);
        }
        return ret;
    } else {
        return null;
    }
}
// ------------------------------------------------------------------------
