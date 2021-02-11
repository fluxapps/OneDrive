import React from "react";
import {Table} from "antd";
import "antd/dist/antd.css";
import columns from "./Columns";
import { withTranslation } from "react-i18next";

class DataTable extends React.Component<any, any> {

    constructor(props: any) {
        super(props);
        // @ts-ignore
        let data: any = window.exod_log_data;
        this.state = {
            data: data.map((set: any) => {
                set.event_type = this.props.t("event_type." + set.event_type);
                set.object_type = this.props.t("object_type." + set.object_type);
                return set;
            })
        }
    }

    render() {
        return (<Table dataSource={this.state.data} columns={columns.map((val) => {
            return {
                dataIndex: val.dataIndex,
                key: val.key,
                title: this.props.t("column." + val.key)
            }
        })}/>);
    }
}

export default withTranslation()(DataTable);
