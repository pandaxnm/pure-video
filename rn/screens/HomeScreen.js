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
    Button,
} from 'react-native';
import Header from '../components/Header';

export default class HomeScreen extends Component{

    constructor(props){
        super(props);
        this.state =  {

        };
    }

  render() {
      return (
          <View style={styles.container}>
              <Header/>
              <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer}>
                  <Button onPress={() => this.props.navigation.navigate('Play')} title={'跳转到播放页'}/>
              </ScrollView>
          </View>
      );
  }

}

HomeScreen.navigationOptions = {
  header: null,
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  contentContainer: {
    // paddingTop: 30,
  },
});
