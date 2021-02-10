import React from "react";
import { Table } from "antd";
import "antd/dist/antd.css";

export default class DataTable extends React.Component<any, any> {
    columns: any = [
        {
            title: 'Id',
            dataIndex: 'id',
            key: 'id'
        },
        {
            title: 'Timestamp',
            dataIndex: 'timestamp',
            key: 'timestamp',
        },
        {
            title: 'Name',
            dataIndex: 'event_type',
            key: 'event_type',
        },
        {
            title: 'Path',
            dataIndex: 'path',
            key: 'path',
        },
        {
            title: 'Object Type',
            dataIndex: 'object_type',
            key: 'object_type',
        },
        {
            title: 'Additional Data',
            dataIndex: 'additional_data',
            key: 'additional_data',
        }
    ];

    constructor(props: any) {
        super(props);
        // @ts-ignore
        let data: any = window.exod_log_data;
        this.state = {
            data: data
        }
    }

    render() {
        console.log(this.state.data);
        return (<Table dataSource={this.state.data} columns={this.columns} />);
    }
}
