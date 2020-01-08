import * as WebBrowser from 'expo-web-browser';
import React, { Component } from 'react';
import {
  Image,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import { Video } from 'expo-av'
import VideoPlayer from 'expo-video-player';
import { Dimensions } from 'react-native';
import Header from '../components/Header';
import { TopNavigationAction } from 'react-native-ui-kitten';
import {Icon} from 'react-native-eva-icons';
import Colors from '../constants/Colors';
import Api from '../constants/Api';

const width = Dimensions.get('window').width;
const height = width/16*9;

export default class PlayScreen extends Component{

    constructor(props){
        super(props);
        this.state =  {

        };
    }

    componentDidMount() {
        Api.playInfo('47', '第01集').then(res => {
            console.log(res);
        })
    }

    render() {

        const BackIcon = (style) => (
            <Icon {...style} name='arrow-ios-back' fill={Colors.white}/>
        );
        const BackAction = (props) => (
            <TopNavigationAction {...props} icon={BackIcon} onPress={()=>this.props.navigation.goBack()}/>
        );

        return (
            <View style={styles.container}>
                <Header title={'视频播放'} leftControl={BackAction()}/>
                <VideoPlayer
                    videoProps={{
                        shouldPlay: true,
                        resizeMode: Video.RESIZE_MODE_CONTAIN,
                        source: {
                            uri: 'http://meng.wuyou-zuida.com/20191229/24867_2b523261/index.m3u8',
                        },
                    }}
                    inFullscreen={true}
                    // videoBackground='transparent'
                    height={height}
                    showFullscreenButton={true}
                />
                <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer}>
                    <Text>1111</Text>
                </ScrollView>
            </View>
        );
    }
}


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  contentContainer: {
    // paddingTop: 30,
  },
});
