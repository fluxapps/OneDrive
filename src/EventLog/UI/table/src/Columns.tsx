const columns = (translate: ((a: string) => string)) : any => {
    return [
        {
            dataIndex: "timestamp",
            key: "timestamp",
            sorter: (a: any, b: any) => Date.parse(a.timestamp) - Date.parse(b.timestamp),
            defaultSortOrder: 'descend',
            title: translate('column.timestamp')
        },
        {
            dataIndex: "user_id",
            key: "user",
            title: translate('column.user')
        },
        {
            dataIndex: "event_type_translated",
            key: "event_type",
            title: translate('column.event_type'),
            filters: [
                { text: translate("event_type.upload_started"), value: 'upload_started' },
                { text: translate("event_type.upload_complete"), value: 'upload_complete' },
                { text: translate("event_type.upload_aborted"), value: 'upload_aborted' },
                { text: translate("event_type.upload_failed"), value: 'upload_failed' },
                { text: translate("event_type.object_deleted"), value: 'object_deleted' },
                { text: translate("event_type.object_renamed"), value: 'object_renamed' },
            ],
            onFilter: (value: string, record: any) => {
                console.log('value: ' + value);
                console.log(record);
                return record.event_type === value;
            }
        },
        {
            dataIndex: "path",
            key: "path",
            title: translate('column.path')
        },
        {
            dataIndex: "object_type_translated",
            key: "object_type",
            title: translate('column.object_type')
        },
        {
            dataIndex: "additional_data_translated",
            key: "additional_data",
            title: translate('column.additional_data')
        }
    ];
};
export default columns;
