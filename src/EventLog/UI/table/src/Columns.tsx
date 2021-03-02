const columns = (translate: ((a: string) => string)) : any => {
    return [
        {
            field: "timestamp",
            title: translate('column.timestamp'),
            defaultSort: "desc"
        },
        {
            field: "user_id",
            title: translate('column.user')
        },
        {
            field: "event_type_translated",
            title: translate('column.event_type'),
        },
        {
            field: "path",
            title: translate('column.path')
        },
        {
            field: "object_type_translated",
            title: translate('column.object_type')
        },
        {
            field: "additional_data_translated",
            title: translate('column.additional_data')
        }
    ];
};
export default columns;
