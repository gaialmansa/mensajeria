/*
  Zerfrex (R) RAD ADM
  Zerfrex RAD for Administration & Data Management

  Copyright (c) 2013-2022 by Jorge A. Montes Pérez <jorge@zerfrex.com>
  All rights reserved. Todos los derechos reservados.

  Este software solo se puede usar bajo licencia del autor.
  El uso de este software no implica ni otorga la adquisición de
  derechos de explotación ni de propiedad intelectual o industrial.
 */


var zfx;
if (!zfx) zfx = {};
if (!zfx.Map) {
    zfx.Map = {};
}

class ZafMap {
    constructor(idMap, editable, center, zoom, tileUrl, saveUrl) {
        if (!editable) {
            this.map = L.map(idMap).setView(center, zoom);
            this.editableLayerGroup = null;
        } else {
            this.editableLayerGroup = new L.LayerGroup();
            this.map = L.map(idMap, {
                editable: true,
                editOptions: {featuresLayer: this.editableLayerGroup}
            }).setView(center, zoom);
            this.editableLayerGroup.addTo(this.map);
        }
        this.saveUrl = saveUrl;
        this.editable = editable;
        L.tileLayer(tileUrl,
            {
                attribution: '<a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 20,
            }
        ).addTo(this.map);
        L.control.scale().addTo(this.map);
        this.layers = {};
        this.layerInfo = {};
        if (editable) this.setupEditable();
    }

    // ------------------------------------------------------------------------

    loadLayer(idLayer, sourceUrl, color, storeOnly) {
        this.layerInfo[idLayer] = {"sourceUrl": sourceUrl, "color": color};
        if (storeOnly) return;
        var me = this;
        $.getJSON(sourceUrl, function (geomData) {
            if (geomData.type != 'FeatureCollection' && geomData[0] == null) {
                return;
            }
            me.layers[idLayer] = L.geoJSON(geomData, {
                style: {
                    "color": color,
                    "weight": 5
                }
            });
            me.layers[idLayer].addTo(me.map);
            me.relocate();
        });
    }

    // ------------------------------------------------------------------------

    loadLayerFC(idLayer, sourceUrl, storeOnly) {
        this.layerInfo[idLayer] = {"sourceUrl": sourceUrl};
        if (storeOnly) return;
        var me = this;
        $.getJSON(sourceUrl, function (geomData) {
            me.layers[idLayer] = L.geoJSON(geomData, {
                style: {
                    "weight": 2
                },
                pointToLayer: function (feature, latlng) {
                    if (feature.properties.planta) {
                        var iconoPlanta = L.icon({
                            iconUrl: 'https://edar.onlineinfosys.com/res/img/icons/planta.png'
                        });
                        return L.marker(latlng, {icon: iconoPlanta});
                    } else if (feature.properties.icon) {
                        var icono = L.icon({
                            iconUrl: feature.properties.icon
                        });
                        return L.marker(latlng, {icon: icono});
                    } else {
                        return L.marker(latlng);
                    }
                },
                onEachFeature: function (feature, layer) {
                    if (feature.properties) {
                        if (feature.properties.popup) {
                            layer.bindPopup(feature.properties.popup);
                        }
                        if (feature.properties.tooltip) {
                            layer.bindTooltip(feature.properties.tooltip);
                        }
                        if (feature.properties.attr && feature.properties.attr.color) {
                            layer.setStyle({"color": feature.properties.attr.color});
                        }
                        if (feature.properties.label) {
                            L.marker(layer.getBounds().getCenter(), {
                                icon: L.divIcon({
                                    className: 'zbfMapLabel',
                                    html: feature.properties.label
                                })
                            }).addTo(me.map);
                        }
                        if (feature.properties.url) {
                            layer.on('click', function (e) {
                                document.location = me.gotoUrl + feature.properties.url;
                            });
                        }
                        if (feature.properties.link) {
                            layer.on('click', function (e) {
                                window.open(feature.properties.link, '_blank');
                            });
                        }
                    }
                }
            });
            me.layers[idLayer].addTo(me.map);
            me.relocate();
        });
    }

    // ------------------------------------------------------------------------

    updateLayer(idLayer, sourceUrl, color, storeOnly) {
        this.layerInfo[idLayer] = {"sourceUrl": sourceUrl, "color": color};
        if (storeOnly) return;
        var me = this;
        $.getJSON(sourceUrl, function (geomData) {
            if (idLayer in me.layers) {
                me.layers[idLayer].removeFrom(me.map);
            }
            me.layers[idLayer] = L.geoJSON(geomData, {
                style: {
                    "color": color,
                    "weight": 5
                },
                onEachFeature: function (feature, layer) {
                    if (feature.properties) {
                        if (feature.properties.popup) {
                            layer.bindPopup(feature.properties.popup);
                        }
                        if (feature.properties.tooltip) {
                            layer.bindTooltip(feature.properties.tooltip);
                        }
                    }
                }
            });
            me.layers[idLayer].addTo(me.map);
        });

    }


    // ------------------------------------------------------------------------

    loadRTLayer(idLayer, sourceUrl) {
        var me = this;
        var l = L.realtime({
            url: sourceUrl,
            crossOrigin: true,
            type: 'json'
        }, {
            interval: 60000,
            cache: true,
            pointToLayer: function (feature, latlng) {
                if (feature.properties.icon) {
                    var icono = L.icon({
                        iconUrl: feature.properties.icon
                    });
                    return L.marker(latlng, {icon: icono});
                } else {
                    return L.marker(latlng);
                }
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties) {
                    if (feature.properties.popup) {
                        layer.bindPopup(feature.properties.popup);
                    }
                    if (feature.properties.tooltip) {
                        layer.bindTooltip(feature.properties.tooltip);
                    }
                }
            }
        });
        l.addTo(this.map);
        let bounds = l.getBounds();
        if (bounds.isValid()) this.map.fitBounds(bounds);
        return l;
    }

    // ------------------------------------------------------------------------

    loadEditableLayer(sourceUrl) {
        var me = this;
        $.getJSON(sourceUrl, function (geomData) {
            var layer = L.geoJSON(geomData);
            layer.eachLayer(function (l) {
                me.editableLayerGroup.addLayer(l);
                l.enableEdit();
            });
            me.relocate();
        });
    }

    // ------------------------------------------------------------------------

    reloadLayers() {
        var me = this;
        for (const idLayer in this.layerInfo) {
            if (idLayer in this.layers) this.layers[idLayer].remove();
            $.getJSON(this.layerInfo[idLayer].sourceUrl, function (geomData) {
                if (geomData[0] == null) return;
                me.layers[idLayer] = L.geoJSON(geomData, {
                    style: {
                        "color": me.layerInfo[idLayer].color,
                        "weight": 5
                    }
                });
                me.layers[idLayer].addTo(me.map);
                me.relocate();
            });
        }
    }

    // ------------------------------------------------------------------------

    relocate() {
        var points = [];
        var b;
        try {
            for (var layer in this.layers) {
                b = this.layers[layer].getBounds();
                if (b) {
                    points.push(b.getNorthWest());
                    points.push(b.getSouthEast());
                }
            }
            if (this.editableLayerGroup) {
                this.editableLayerGroup.eachLayer(function (l) {
                    b = l.getBounds();
                    points.push(b.getNorthWest());
                    points.push(b.getSouthEast());
                });
            }
            if (points.length > 0) {
                this.bounds = L.latLngBounds(points);
                this.map.fitBounds(this.bounds);
            }
        } catch (error) {
            console.log(error);
        }
    }

    // ------------------------------------------------------------------------

    setupEditable() {
        var me = this;
        L.NewLineControl = L.Control.extend({
            options: {
                position: 'topleft',
            },
            onAdd: function (map) {
                var container = L.DomUtil.create('div', 'leaflet-control leaflet-bar'),
                    link = L.DomUtil.create('a', '', container);
                link.href = '#';
                link.title = 'Crear segmento';
                link.innerHTML = '<i class="fas fa-bezier-curve"></i>';
                L.DomEvent.on(link, 'click', L.DomEvent.stop).on(link, 'click', function () {
                    me.map.editTools.startPolyline();
                });
                return container;
            }
        });
        L.SaveAllControl = L.Control.extend({
            options: {
                position: 'topleft'
            },
            onAdd: function (map) {
                var container = L.DomUtil.create('div', 'leaflet-control leaflet-bar'),
                    link = L.DomUtil.create('a', '', container);
                link.href = '#';
                link.title = 'Guardar cambios';
                link.innerHTML = '<i class="fas fa-save"></i>';
                L.DomEvent.on(link, 'click', L.DomEvent.stop).on(link, 'click', function () {
                    me.saveEditableLayer();
                });
                return container;
            }
        });
        this.map.addControl(new L.NewLineControl());
        this.map.addControl(new L.SaveAllControl());
        this.map.on('layeradd', function (event) {
            if (event.layer instanceof L.Path) event.layer.on('click', L.DomEvent.stop).on('click', function (ev) {
                if ((ev.originalEvent.ctrlKey || ev.originalEvent.metaKey) && this.editEnabled()) this.editor.deleteShapeAt(ev.latlng);
            }, event.layer);
            if (event.layer instanceof L.Path) event.layer.on('dblclick', L.DomEvent.stop).on('dblclick', event.layer.toggleEdit);
        });
    }

    // ------------------------------------------------------------------------

    saveEditableLayer() {
        if (this.editableLayerGroup) {
            $.post(this.saveUrl, {GeoJSON: JSON.stringify(this.editableLayerGroup.toGeoJSON(6))});
        }
    }

    // ------------------------------------------------------------------------

    addBackButton(url) {
        var me = this;
        L.BackControl = L.Control.extend({
            options: {
                position: 'topleft',
            },
            onAdd: function (map) {
                var container = L.DomUtil.create('div', 'leaflet-control leaflet-bar');
                var link = L.DomUtil.create('a', '', container);
                link.href = url;
                link.title = 'Volver';
                link.innerHTML = '<img src="https://edar.onlineinfosys.com/res/img/icons/arrow-go-back-fill.svg" style="margin: 2px;""></img>';
                return container;
            }
        });
        this.map.addControl(new L.BackControl());
    }


}
