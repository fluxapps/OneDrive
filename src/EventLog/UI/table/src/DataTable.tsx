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
                set.event_type_translated = this.props.t("event_type." + set.event_type);
                set.object_type_translated = this.props.t("object_type." + set.object_type);
                set.additional_data_translated = this.formatAdditionalData(set.additional_data);
                return set;
            })
        }
    }

    render() {
        return (<Table dataSource={this.state.data} columns={columns(this.props.t)}/>);
    }

    formatAdditionalData(additional_data: string) {
        let parsed = JSON.parse(additional_data);
        let string = '';
        Object.keys(parsed).map((key: string) => {
           string += this.props.t("additional_data." + key) + ": " + parsed[key]
        });
        return string;
    }
}

export default withTranslation()(DataTable);
