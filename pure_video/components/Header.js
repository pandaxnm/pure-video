import React from 'react';
import {Platform}  from "react-native";
import { TopNavigation} from 'react-native-ui-kitten';
import {SafeAreaView,} from 'react-navigation';
import Colors from "../constants/Colors";
import Constants from 'expo-constants';

const paddingTop = Platform.OS === 'android' ? Constants.statusBarHeight : 0;

const Header = (props) => (
    <SafeAreaView style={{backgroundColor: Colors.tintColor,paddingTop:paddingTop}}>
        <TopNavigation
            style={{backgroundColor:Colors.tintColor}}
            alignment='center'
            titleStyle={{color:Colors.white,fontSize:17}}
            {...props}
        />
    </SafeAreaView>
);
export default Header;
