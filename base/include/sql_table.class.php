<?php
    class SqlTable
    {
        private $tblName  = "";
        private $idName   = "";
        /** @var connect_db */
        private $DB        = null;
        public $fields     = [];
        public $error      = '';
        public $json       = false;

        public function __construct($sName, $obj = null, $info = [], $sIdFieldName = 'id', $json = false, $db = null) {
            global $DB;
            $this->tblName  = $sName;
            $this->idName	= $sIdFieldName;
            $this->DB       = $db ? $db : $DB;
            $this->json     = $json;
            $exclude = $info && isset($info['exclude']) ? $info['exclude'] : [];
            $integer = $info && isset($info['integer']) ? $info['integer'] : [];
            $rename  = $info && isset($info['rename'])  ? $info['rename']  : [];
            if(!$exclude && !$integer && !$rename && $info) {
                $exclude = $info;
            }
            if($obj && is_object($obj)) {
                foreach($obj as $key => $val) {
                    if($exclude && in_array($key, $exclude)) continue;
                    if(is_a($val, 'Text')) continue;
                    if($integer && in_array($key, $integer)) $val = intval($val);

                    if($rename && exists($rename[$key])) $key = $rename[$key];
                    $this->addFld($key, $val);
                }
            }
        }

        public function addNul($sField, $oVal) {
            $sVal = $oVal;
            if(is_null($oVal)) {
				$sVal = "NULL";
            } elseif(is_bool($oVal)) {
                $sVal = $oVal ? 'TRUE' : 'FALSE';
			} elseif(is_float($oVal)) {
				$sVal = str_replace(',', '.', sprintf('%f', $oVal));
			} elseif(is_int($oVal)) {
				$sVal = sprintf('%u', $oVal);
			} elseif($oVal instanceof DateTime) {
				$sVal = $oVal->format('Y-m-d H:i:s');
            } elseif($oVal instanceof StMultiPolygon) {
                $sVal = "GeomFromText('" . $oVal->toString() . "')";
            } elseif($oVal instanceof StPolygon) {
                $sVal = "GeomFromText('" . $oVal->toString() . "')";
            } elseif($oVal instanceof StPolyline) {
                $sVal = "GeomFromText('" . $oVal->toString() . "')";
            } elseif($oVal instanceof StPoint) {
                $sVal = "POINT(" . $oVal->toCommaString() . ")";
            } else {
            	if(empty($oVal)) {
            		$sVal = "NULL";
				}
			}
            $this->fields[$sField] = $sVal;
        }

		public function addFld($sField, $oVal) {
            $sVal = $oVal;
            if($this->json) {
                if($oVal instanceof DateTime) {
                    $sVal = $oVal->format("Y-m-d H:i:s");
                } elseif($oVal instanceof StMultiPolygon) {
                    $sVal = "GeomFromText('" . $oVal->toString() . "')";
                } elseif($oVal instanceof StPolygon) {
                    $sVal = "GeomFromText('" . $oVal->toString() . "')";
                } elseif($oVal instanceof StPolyline) {
                    $sVal = "GeomFromText('" . $oVal->toString() . "')";
                } elseif($oVal instanceof StPoint) {
                    $sVal = "POINT(" . $oVal->toCommaString() . ")";
                } elseif($oVal instanceof GMP) {
                    $sVal = gmp_strval($oVal);
                } elseif(is_object($oVal)) {
                    if(method_exists($oVal, 'getSimple')) {
                        $sVal = $oVal->getSimple();
                    }
                }
            } else {
                if(is_null($oVal)) {
    				$sVal = "";
    			} elseif(is_float($oVal)) {
    				$sVal = str_replace(',', '.', sprintf('%f', $oVal));
    			} elseif(is_int($oVal)) {
    				$sVal = sprintf('%u', $oVal);
                } elseif(is_bool($oVal)) {
                    $sVal = $oVal ? 1 : 0;
                } elseif($oVal instanceof GMP) {
                    $sVal = gmp_strval($oVal);
    			} elseif($oVal instanceof DateTime) {
    				$sVal = $oVal->format('Y-m-d H:i:s');
                } elseif($oVal instanceof StMultiPolygon) {
                    $sVal = "GeomFromText('" . $oVal->toString() . "')";
                } elseif($oVal instanceof StPolygon) {
                    $sVal = "GeomFromText('" . $oVal->toString() . "')";
                } elseif($oVal instanceof StPolyline) {
                    $sVal = "GeomFromText('" . $oVal->toString() . "')";
                } elseif($oVal instanceof StPoint) {
                    $sVal = "POINT(" . $oVal->toCommaString() . ")";
                } elseif(is_object($oVal)) {
                    if(property_exists($oVal, 'id')) {
                        $sVal = $oVal->id;
                    } elseif(property_exists($oVal, 'guid')) {
                        $sVal = $oVal->guid;
                    } else {
                        $sVal = json_encode($oVal, JSON_UNESCAPED_UNICODE);
                    }
                } elseif(empty($oVal)) {
            		$sVal = '';
    			}
            }
            $this->fields[$sField] = $sVal;
		}

        private function update($obj)
        {
            $idf = $this->idName;
			$fld = [];
            $par = [];
            $ix = 1;
			foreach ($this->fields as $k => $v) {
                $f = "fld{$ix}";
				$fld[] = "`{$k}` = :{$f}";
                $par[$f] = $v;
                $ix++;
			}

            $flds = implode(', ', $fld);

			$this->DB->prepare("UPDATE `{$this->tblName}` SET $flds WHERE `{$idf}` = :id")
                     ->bind('id', $obj->$idf);
            foreach($par as $k => $v) $this->DB->bind($k, $v);
            $q = $this->DB->execute();
            $this->error = $this->DB->error;
			return $q ? TRUE : FALSE;
        }

        private function insert($obj, $dup_upd)
        {
            $upd = [];
            $par = [];
            $val = [];
            $ix = 1;
            foreach ($this->fields as $k => $v) {
                $f = "fld{$ix}";
                $upd[] = "`{$k}` = :{$f}";
                $val[] = ":{$f}";
                $par[$f] = $v;
                $ix++;
            }

            $upds = implode(', ', $upd);
            $flds = "`" . implode('`, `', array_keys($this->fields)) . "`";
			$vals = implode(', ', $val);
            $upds = $dup_upd ? (' ON DUPLICATE KEY UPDATE ' . implode(', ', $upd)) : '';
			$this->DB->prepare("INSERT INTO `$this->tblName` ($flds) VALUES ($vals)$upds");
            foreach($par as $k => $v) $this->DB->bind($k, $v);
            $q = $this->DB->execute();
            $this->error = $this->DB->error;
			if($q) {
                $id = $this->DB->lastInsertId();
                $idf = $this->idName;
				if($id && $idf) $obj->$idf = $id;
			}
			return $q ? TRUE : FALSE;
        }

        public function save($obj, $dup_upd = false) {
            $ret = FALSE;
            $idf = $this->idName;
            if(!isset($obj->$idf)) { $obj->$idf = 0; }
			if ($obj->$idf < 0 || empty($this->fields)) return $ret;
			if ($obj->$idf == 0 || $dup_upd) {
				$ret = $this->insert($obj, $dup_upd);
            } else {
			    $ret = $this->update($obj);
            }
            return $ret;
        }
	}
