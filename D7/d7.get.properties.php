<?
$dbElement = ElementTable::getList([
    'select' => ['ID', 'NAME', 'CREATED_BY', 'DATE_CREATE', 'CLIENT', 'STATUS', 'BP_ID', 'TASK_ID', 'CURRENT_APPROVER'],
    'filter' => [
        '=IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
        "=CLIENT" => $this->arParams['CLIENT_ID'],
        "!=BP_ID" => "",
        '=CLIENT_PROP.CODE' => 'CLIENT',
        '=STATUS_PROP.CODE' => 'STATUS',
        '=BP_ID_PROP.CODE' => 'BP_ID',
        '=TASK_ID_PROP.CODE' => 'TASK_ID',
        '=CURRENT_APPROVER_PROP.CODE' => 'CURRENT_APPROVER',
        [
            "LOGIC" => "AND",
            ["!STATUS" => 'null'],
            ["<STATUS" => strval($stat[count($stat) - 1])]
        ],
    ],
    'order' => ['DATE_CREATE' => 'DESC'],
    'runtime' => getProps(['CLIENT', 'STATUS', 'BP_ID', 'TASK_ID', 'CURRENT_APPROVER'])
]);

/**
 * Массив свойств элемента инфоблока для выборки
 * @param $arProps
 * @return array
 */
public static function getProps($arProps)
{
    $result = [];
    foreach ($arProps as $code) {
        $result[$code . '_PROP'] = [
            'data_type' => 'Bitrix\Iblock\PropertyTable',
            'reference' => ['=this.IBLOCK_ID' => 'ref.IBLOCK_ID'],
            'join_type' => "LEFT"
        ];
        $result[$code] = [
            'data_type' => 'float',
            'expression' => [
                '(SELECT b_iblock_element_property.VALUE 
                  FROM b_iblock_element_property 
                  WHERE b_iblock_element_property.IBLOCK_PROPERTY_ID=%s AND b_iblock_element_property.IBLOCK_ELEMENT_ID=%s
                )',
                $code . '_PROP.ID',
                'ID',
            ],
        ];
    }
    return $result;
}